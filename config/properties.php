<?php

return [
	// user properties
	'user' => [
		// 'key|hash' => 'id',
		'author|function' => 'getName'
	],

	// format groups -> format -> attribute
	'format' => [
		'name',
		'formats|many' => [
			'id',
			// 'key|hash' => 'id',
			'format_group_id',
			'name',
			'value',
			'combination|many' => [
				'id',
				// 'key|hash' => 'id',
				'format_id',
				'attribute_type_id',
				'price',
				'color_price',
				'attribute|many' => [
					'id',
					'keycode',
					'name',
					'order_number',
				],
			],
		],
	],

	// story properties
	'story' => [
		'id',
		// 'key|hash' => 'id',
		'is_file_type',
		'file_url',
        'user_id',
        'title',
        'author|inline|user|space' => 'firstname|lastname',
        'story_date',
        'from_date',
        'to_date',
        'pages|many' => [
			'id', 
			// 'key|hash' => 'id',
			'content', 
			'is_colored',
		],
		'total_page',
        'colored_index',
	],

	'list' => [
		'id',
		// 'key|hash' => 'id',
		'user_id',
		'title',
        'author|inline|user|space' => 'firstname|lastname',
		'from_date',
		'story_date',
		'to_date',
		'pages|many' => [
			'id', 
			// 'key|hash' => 'id',
			'content', 
			'is_colored',
		],
		'shared_to|alias' => [
			'shares|many' => [
				'id', 
				// 'key|hash' => 'id',
				'email', 
				'is_allow_edit',
			],
		],
		'tags',
    	'total_page',
        'colored_index',
	],
	

	// book publish properties
	'publish' => [
        'id',
		// 'key|hash' => 'id',
		'user_id',
        'author|inline|user|space' => 'firstname|lastname',
        'title',
        'price',
        'original_price',
        'markup_price',
        'ebook_price',
        'ebook_markup_price',
        
        'chapters|many' => [
			'id',
			'book_id',
        	'is_file_type',
        	'title',
            'file_url',
        	'position',
        	'author|inline|stories:user|space' => 'firstname|lastname',

        	'from_date|inline|stories' => 'from_date',
        	'to_date|inline|stories' => 'to_date',
        	'story_date|inline|stories' => 'story_date',

        	'pages|many' => [
				'id',
				'book_chapter_id',
        		'content',
        		'is_colored',
        	],
        	'total_page',
        	'colored_index',
        ],
        'description',
        'tags' ,
        'status_type',
        
    	'total_page',
	],

	// book properties
	'book' => [
		'id',
		// 'key|hash' => 'id',
        'user_id',
        'author|inline|user|space' => 'firstname|lastname',
        'title',
        'content',
        'description',
        'tags',

        'chapters|many' => [
			'id',
			'book_id',
			'story_id',
        	'is_file_type',
            'title',
            'file_url',
        	'position',
        	'tags|inline|stories' => 'tags',
        	'shared_to|null' => [],
        	'author|inline|stories:user|space' => 'firstname|lastname',

        	'from_date|inline|stories' => 'from_date',
        	'to_date|inline|stories' => 'to_date',
        	'story_date|inline|stories' => 'story_date',

        	'pages|many' => [
				'id',
				'book_chapter_id',
        		'content',
        		'is_colored'
        	],
        	'total_page',
        	'colored_index',
        ],

        'price',
        'original_price',
        'markup_price',
        'ebook_price',
        'ebook_markup_price',

        'status_type',

        'book_date',
        'from_date',
        'to_date',

        'is_deleted',
        'is_save_as_draft',

    	'total_page',
	],

	// cart properties
	'cart' => [
		'id',
		// 'key|hash' => 'id',
		'product_id',
        'author|inline|user|space' => 'firstname|lastname',
		'title',
		'description',
		'quantity',
		'unit_price',
	],
];