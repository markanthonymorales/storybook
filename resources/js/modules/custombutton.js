import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview'

const CustomButtonPlugin = async ( editor ) => {
	console.log('CustomButton')

    editor.ui.componentFactory.add( 'customButton', locale => {
        const view = new ButtonView( locale );

        view.set( {
            label: 'Custom Button',
            tooltip: true
        } );

        // Callback executed once the image is clicked.
        view.on( 'execute', () => {
            alert('Test')
        } );

        return view;
    });
}

export { CustomButtonPlugin }