/**
 * Adds a tinymce plugin that will insert the roxi_widget short tag into the editor when a button is clicked.
 */
tinymce.PluginManager.add( 'roxi_plugin', function( editor, url ) {

	// add command to insert the short code
	editor.addCommand('insert_roxi_shortcode', function() {
    	content = '[roxi_widget]';

        tinymce.execCommand('mceInsertContent', false, content);
    });

	// add button, set it to trigger the short code command
	editor.addButton( 'roxi_button', {
		image: url + '/img/roxi.png',
		tooltip: 'Insert Roxi Widget',
		cmd: 'insert_roxi_shortcode'
	});
});