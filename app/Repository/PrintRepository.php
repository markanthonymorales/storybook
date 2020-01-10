<?php

namespace App\Repository;

use Carbon\Carbon;

use App\Format;
use App\Attribute;
use App\FormatAttribute;
use App\Configuration;
use App\Book;

use Auth;
class PrintRepository {
	
	public static function calculation($request)
	{
		$user = Auth::user();
        $book = Book::find($request->id);

        $cover_cost = self::getCover($request);
        $manufacturing_cost = self::getManufacturing($request);
        $markup_price = Configuration::where('keycode', 'markup_price')->firstOrFail();
        $retail_price =+ $manufacturing_cost + ($manufacturing_cost * ($markup_price->value / 100));

        return [
            'success' => true,
        	'result' => [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,

                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->user->lastname.', '.$book->user->firstname,
                
                'total_height' => $cover_cost->total_height,
                'total_width' => $cover_cost->total_width,
                'total_height_incl_bleed' => $cover_cost->total_height_incl_bleed,
                'total_width_incl_bleed' => $cover_cost->total_width_incl_bleed,

                'total_book' => $request->total_book,
                'total_page' => $request->total_page,
                'colored_index' => $request->colored_index,
                'total_colored_page' => $request->total_colored_page,

                'paper' => Attribute::find($request->paper),
                'binding' => Attribute::find($request->binding),
                'cover' => Attribute::find($request->cover),
                'lamination' => Attribute::find($request->lamination),

                'manufacturing_cost' => $manufacturing_cost,
                'retail_price' => $retail_price
            ],
            'cover_cost' => $cover_cost,
            'manufacturing_cost' => $manufacturing_cost,
            'retail_price' => $retail_price,
            'shipping_cost' => self::getShipping($request)
        ];
	}

	public static function getShipping($request)
	{
		$standard = [ '4.70', '5.90', '8.90', '12.40' ];
        $express = [ '12.00', '15.00', '19.00', '24.00' ];
        $total_book = intval($request->total_book);

        $s = 0;
        if($total_book > 4)
            $s = 1;

        if($total_book > 16)
            $s = 2;

        if($total_book > 32)
            $s = 3;

        return [
            'standard' => $standard[$s],
            'express' => $express[$s],
        ];
	}

	public static function getCover($request)
	{
		$paper = Attribute::find($request->paper);
        $cover = Attribute::find($request->cover);

        $widthChoose = [ 0, 0.5, 0.5, 0.7, 0.7 ];
        $heightChoose = [ 0,0.6,0.6,0.6,0.6 ];
        $bleedChoose = [ 0.5,1.7,1.7,0.5,0.5 ];
        $spineChoose = [
            [ 0.012,0.0105,0.0135,0.0144,0.0126 ],
            [ 0,0.35,0.5,0.35,0.5 ]
        ];

        $format = Format::find($request->book_format);

        $dimension = explode('x', $format->value);
        $width_front_cover = floatval($dimension[0]) + floatval($widthChoose[$cover->order_number - 1]);
        $height_front_cover = floatval($dimension[1]) + floatval($heightChoose[$cover->order_number - 1]);
        
        $spine = floatval($request->total_page) / 2 * floatval($spineChoose[0][$paper->order_number - 1]) + floatval($spineChoose[1][$cover->order_number - 1]);

        if($cover->order_number > 1 && $request->total_page > 299)
            $spine += 0.1;

        $bleed = floatval($bleedChoose[$cover->order_number - 1]);

        $total_width = floatval($width_front_cover) * 2 + floatval($spine);
        
        if($cover->order_number > 4){
            $total_width += (9*2);
        }

        $total_width_incl_bleed = floatval($total_width) + (floatval($bleed) * 2);
        $total_height = $height_front_cover;
        $total_height_incl_bleed = floatval($height_front_cover) + (floatval($bleed) * 2);

        return (object)[
            'width_front_cover' => $width_front_cover,
            'height_front_cover' => $height_front_cover,
            'spine' => $spine,
            'bleed' => $bleed,
            'total_width' => $total_width,
            'total_width_incl_bleed' => $total_width_incl_bleed,
            'total_height' => $total_height,
            'total_height_incl_bleed' => $total_height_incl_bleed,
        ];
	}

	public static function getManufacturing($request)
	{
		$format_paper = FormatAttribute::where('format_id', $request->book_format)
        ->where('attribute_type_id', $request->paper)
        ->firstOrFail();

        $format_cover = FormatAttribute::where('format_id', $request->book_format)
        ->where('attribute_type_id', $request->cover)
        ->firstOrFail();

        $manufacturing_cost = (
            (
                $request->total_page - ($request->has_color?$request->total_colored_page:0)
            ) * $format_paper->price
        ) + (
                ($request->has_color?$request->total_colored_page:0) * $format_paper->color_price
        ) + $format_cover->price;

        if($request->binding){
            $format_binding = FormatAttribute::where('format_id', $request->book_format)
            ->where('attribute_type_id', $request->binding)
            ->firstOrFail();

            $manufacturing_cost += $format_binding->price;
        }

        if($request->lamination){
            $format_lamination = FormatAttribute::where('format_id', $request->book_format)
            ->where('attribute_type_id', $request->lamination)
            ->firstOrFail();
            $manufacturing_cost += $format_lamination->price;
        }

        $manufacturing_cost *= $request->total_book;

        return $manufacturing_cost;
	}

	public static function getXML($request)
	{
		$dt = Carbon::now();
        return '<?xml version="1.0" encoding="UTF-8"?>
            <BoD>
                <Header>
                    <FromCompany>BoD</FromCompany>
                    <FromCompanyNumber>'.rand(1111111111,9999999999).'</FromCompanyNumber>
                    <SentDate>'.$dt->year.$dt->month.$dt->day.'</SentDate>
                    <SentTime>'.$dt->hour.':'.$dt->minute.'</SentTime>
                    <FromPerson>'.$request->firstname.' '.$request->lastname.'</FromPerson>
                    <FromEmail>'.$request->email.'</FromEmail>
                </Header>
                <CustomBookOrder>
                    <Item>
                        <ItemNumber>'.$request->id.'</ItemNumber>
                        <Title>'.$request->title.'</Title>
                        <Contributor>
                            <ContributorRole>author</ContributorRole>
                            <ContributorName>'.$request->author.'</ContributorName>
                        </Contributor>
                        <Height>'.$request->total_height.'</Height>
                        <Width>'.$request->total_width.'</Width>
                        <Pages>'.$request->total_page.'</Pages>
                        <ColouredPages>'.$request->total_colored_page.'</ColouredPages>
                        <ColouredPagesPosition>'.$request->colored_index.'</ColouredPagesPosition>
                        <Paper>'.($request->paper?$request->paper['name']:'').'</Paper>
                        <Binding>'.($request->binding?$request->binding['name']:'').'</Binding>
                        <Back>'.($request->cover?$request->cover['name']:'').'</Back>
                        <Finish>'.($request->lamination?$request->lamination['name']:'').'</Finish>
                        <Jacket>no</Jacket>
                    </Item>
                    <Order>
                        <OrderNumber>'.rand(1111111111,9999999999).'</OrderNumber>
                        <Copies ItemNumber="'.$request->id.'">'.$request->total_book.'</Copies>
                        <AddressLine1>'.$request->address.'</AddressLine1>
                        <Street>'.$request->street.'</Street>
                        <ZIP>'.$request->zipcode.'</ZIP>
                        <City>'.$request->city.'</City>
                        <Country>'.$request->country.'</Country>
                        <InvoiceFile>'.preg_replace("/[\s-]/", "_",strtolower($request->title)).'.epub</InvoiceFile>
                        <CustomsValue>'.$request->shipping_price.'</CustomsValue>
                        <Shipping>'.$request->shipping_option.'</Shipping>
                    </Order>
                </CustomBookOrder>
            </BoD>';
	}

	public static function handleData($request)
	{
		return [
            'name'=> $request->fullname,
            'to'=> $request->email,
            'amount'=> floatval($request->total_price),

            'title' => $request->title,
            'author' => $request->firstname.' '.$request->lastname,
            
            'with' => $request->total_width,
            'height' => $request->total_height,
            'total_page' => $request->total_page,
            'total_colored_page' => $request->total_colored_page,
            'colored_index' => $request->colored_index,
            'paper' => $request->paper?$request->paper['name']:'',
            'binding' => $request->binding?$request->binding['name']:'',
            'cover' => $request->cover?$request->cover['name']:'',
            'lamination' => $request->lamination?$request->lamination['name']:'',

            'address' => $request->address,
            'street' => $request->street,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'country' => $request->country,

            'shipping_option' => $request->shipping_option,
            'shipping_price' => $request->shipping_price,
        ];
	}
}