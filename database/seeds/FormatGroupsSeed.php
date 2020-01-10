<?php

use Illuminate\Database\Seeder;

class FormatGroupsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->insert([
        	[
				'keycode' => 'markup_price',
				'name' => 'Markup Price',
				'description' => 'Markup Price',
				'value' => 5
			]
        ]);

        DB::table('format_groups')->insert([
        	[
				'name' => 'Toner Premium',
			],
			[
				'name' => 'Ink Jet',
			],
        ]);

        DB::table('formats')->insert([
        	// Toner Premium
        	[
				'format_group_id' => 1,
				'name' => 'A5',
				'value' => '15x15',
			],
			[
				'format_group_id' => 1,
				'name' => 'A5',
				'value' => '20x20',
			],
			[
				'format_group_id' => 1,
				'name' => 'A4',
				'value' => '25x25',
			],
			[
				'format_group_id' => 1,
				'name' => 'A5',
				'value' => '20x20',
			],

			// Ink Jet
			[
				'format_group_id' => 2,
				'name' => 'A5',
				'value' => '15x15',
			],
			[
				'format_group_id' => 2,
				'name' => 'A5',
				'value' => '20x20',
			],
			[
				'format_group_id' => 2,
				'name' => 'A4',
				'value' => '25x25',
			],
			[
				'format_group_id' => 2,
				'name' => 'A5',
				'value' => '20x20',
			],
        ]);

        // Insert Paper type
        DB::table('attribute_types')->insert([
        	[
				'keycode' => 'paper_type',
				'name' => 'SW Seite 80/90g Papier',
				'order_number' => 1
			],
			[
				'keycode' => 'paper_type',
				'name' => 'SW Seite 120g Papier',
				'order_number' => 2
			],
			[
				'keycode' => 'paper_type',
				'name' => 'SW Seite 200g Papier',
				'order_number' => 3
			],
        ]);

        // Insert Cover type
        DB::table('attribute_types')->insert([
        	[
				'keycode' => 'cover_type',
				'name' => 'PB 4/0 Cover',
				'order_number' => 1
			],
			[
				'keycode' => 'cover_type',
				'name' => 'PB 4/4 Cover',
				'order_number' => 2
			],
			[
				'keycode' => 'cover_type',
				'name' => 'HC Cover (runder Rücken)',
				'order_number' => 3
			],
			[
				'keycode' => 'cover_type',
				'name' => 'HC Cover (gerader Rücken)',
				'order_number' => 4
			],
			[
				'keycode' => 'cover_type',
				'name' => 'HC Cover (Schutzumschlag)',
				'order_number' => 5
			],
        ]);

        // Insert Lamination type
        DB::table('attribute_types')->insert([
        	[
				'keycode' => 'lamination_type',
				'name' => 'Aufschlag für struk. Laminat',
				'order_number' => 1
			],
        ]);

        // Insert Binding type
        DB::table('attribute_types')->insert([
        	[
				'keycode' => 'binding_type',
				'name' => 'Aufschlag für Fadenbindung (nur HC, Toner)',
				'order_number' => 1
			],
        ]);

        // Insert Calculation of Formats and Paper Types
        DB::table('format_attribute')->insert([
			// Toner Premium
        	// A5 (15x15)
        	[
				'format_id' => 1,
				'attribute_type_id' => 1,
				'price' => '0.0170',
				'color_price' => '0.1000',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 2,
				'price' => '0.0190',
				'color_price' => '0.1200',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 3,
				'price' => '0.0210',
				'color_price' => '0.1400',
			],

			// A5 (20x20)
        	[
				'format_id' => 2,
				'attribute_type_id' => 1,
				'price' => '0.0180',
				'color_price' => '0.1100',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 2,
				'price' => '0.0200',
				'color_price' => '0.1300',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 3,
				'price' => '0.0220',
				'color_price' => '0.1500',
			],

			// Toner Premium
			// A4 (25x25)
        	[
				'format_id' => 3,
				'attribute_type_id' => 1,
				'price' => '0.0190',
				'color_price' => '0.1200',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 2,
				'price' => '0.0210',
				'color_price' => '0.1400',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 3,
				'price' => '0.0230',
				'color_price' => '0.1600',
			],
        ]);

        // Insert Calculation of Formats and Cover Types
        DB::table('format_attribute')->insert([
			// Toner Premium
			// A5 (15x15)
        	[
				'format_id' => 1,
				'attribute_type_id' => 4,
				'price' => '1.00',
				'color_price' => '0',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 5,
				'price' => '1.10',
				'color_price' => '0',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 6,
				'price' => '4.80',
				'color_price' => '0',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 7,
				'price' => '4.80',
				'color_price' => '0',
			],
			[
				'format_id' => 1,
				'attribute_type_id' => 8,
				'price' => '4.80',
				'color_price' => '0',
			],

			// Toner Premium
			// A5 (20x20)
			[
				'format_id' => 2,
				'attribute_type_id' => 4,
				'price' => '1.10',
				'color_price' => '0',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 5,
				'price' => '1.20',
				'color_price' => '0',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 6,
				'price' => '5.30',
				'color_price' => '0',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 7,
				'price' => '5.30',
				'color_price' => '0',
			],
			[
				'format_id' => 2,
				'attribute_type_id' => 8,
				'price' => '5.30',
				'color_price' => '0',
			],

			// Toner Premium
			// A4 (25x25)
			[
				'format_id' => 3,
				'attribute_type_id' => 4,
				'price' => '1.20',
				'color_price' => '0',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 5,
				'price' => '1.30',
				'color_price' => '0',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 6,
				'price' => '5.80',
				'color_price' => '0',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 7,
				'price' => '5.80',
				'color_price' => '0',
			],
			[
				'format_id' => 3,
				'attribute_type_id' => 8,
				'price' => '5.80',
				'color_price' => '0',
			],
        ]);

        // Insert Calculation of Formats and Lamination Types
        DB::table('format_attribute')->insert([
			// Toner Premium
			// A5 (15x15)
        	[
				'format_id' => 1,
				'attribute_type_id' => 9,
				'price' => '0.17',
				'color_price' => '0',
			],

			// Toner Premium
			// A5 (20x20)
			[
				'format_id' => 2,
				'attribute_type_id' => 9,
				'price' => '0.17',
				'color_price' => '0',
			],

			// Toner Premium
			// A4 (25x25)
			[
				'format_id' => 3,
				'attribute_type_id' => 9,
				'price' => '0.17',
				'color_price' => '0',
			],
        ]);

        // Insert Calculation of Formats and Binding Types
        DB::table('format_attribute')->insert([
			// Toner Premium
			// A5 (15x15)
        	[
				'format_id' => 1,
				'attribute_type_id' => 10,
				'price' => '2.50',
				'color_price' => '0',
			],

			// Toner Premium
			// A5 (20x20)
			[
				'format_id' => 2,
				'attribute_type_id' => 10,
				'price' => '2.50',
				'color_price' => '0',
			],

			// Toner Premium
			// A4 (25x25)
			[
				'format_id' => 3,
				'attribute_type_id' => 10,
				'price' => '2.50',
				'color_price' => '0',
			],
        ]);
    }
}
