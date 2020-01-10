<?php

namespace App\Repository;

use App\Book;
use App\Order;
use Auth;

class OrderRepository{
	
    public static function handleData($request, $amount)
    {
        return [
            'name'=> $request->fullname,
            'to'=> $request->email,
            'amount'=> $amount,
        ];
    }

	public static function setData($request)
    {
        $user = Auth::user();
    	$order = new Order;
        $order->book_id = $request->id;
        $order->user_id = $user->id;

    	$order->fullname = $request->fullname;
        $order->email = $request->email;

        $order->title = $request->title;
        $order->author = $request->author;

        $order->total_height = floatval($request->total_height);
        $order->total_width = floatval($request->total_width);
        $order->total_height_incl_bleed = floatval($request->total_height_incl_bleed);
        $order->total_width_incl_bleed = floatval($request->total_width_incl_bleed);

        $order->total_book = intval($request->total_book);
        $order->total_page = intval($request->total_page);
        $order->colored_index = $request->colored_index;
        $order->total_colored_page = intval($request->total_colored_page);

        $order->paper_id = $request->paper['id'];
        $order->binding_id = $request->binding['id'];
        $order->cover_id = $request->cover['id'];
        $order->lamination_id = $request->lamination['id'];

        $order->manufacturing_cost = floatval($request->manufacturing_cost);
        $order->retail_price = floatval($request->retail_price);

        $order->shipping_option = $request->shipping_option;
        $order->shipping_price = floatval($request->shipping_price);
        $order->book_price = floatval($request->book_price);
        $order->total_price = floatval($request->total_price);

        $order->address1 = $request->address;
        $order->address2 = '';
        $order->street = $request->street;
        $order->city = $request->city;
        $order->zipcode = $request->zipcode;
        $order->country = $request->country;
        $order->xml_directory = $request->xml_directory;
    	$order->save();
    	return $order;
    }

    public static function getCart($cart)
    {
        $result = [
            'items' => self::getItems($cart->items),
            'totalPrice' => $cart->total_price|0.00,
            'totalQty' => $cart->item_count|0,
        ];

        return $result;
    }

    public static function getItems($items)
    {
        if(!$items)
            return [];

        $newItems = [];
        foreach ($items as $i => $item) {
            $book = Book::find($item->product_id);
            $newItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'title' => $book->title,
                'author' => $book->user->firstname.' '.$book->user->lastname,
                'description' => $book->description,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price
            ];
        }

        return $newItems;
    }

    public static function convertToValidPrice($price) {
        $price = str_replace(['-', ',', '$', ' '], '', $price);
        if(!is_numeric($price)) {
            $price = null;
        } else {
            if(strpos($price, '.') !== false) {
                $dollarExplode = explode('.', $price);
                $dollar = $dollarExplode[0];
                $cents = $dollarExplode[1];
                if(strlen($cents) === 0) {
                    $cents = '00';
                } elseif(strlen($cents) === 1) {
                    $cents = $cents.'0';
                } elseif(strlen($cents) > 2) {
                    $cents = substr($cents, 0, 2);
                }
                $price = $dollar.'.'.$cents;
            } else {
                $cents = '00';
                $price = $price.'.'.$cents;
            }
        }

        return $price;
    }
}