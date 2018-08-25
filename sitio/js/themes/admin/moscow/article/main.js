var categories = document.getElementById( 'categories' );
multi( categories, {
    'enable_search': true,
    'search_placeholder': 'Buscar categoria...',
    'non_selected_header': null,
    'selected_header': null,
    'limit': -1,
    'limit_reached': function () {},
});

var tags = document.getElementById( 'tags' );
multi( tags, {
    'enable_search': true,
    'search_placeholder': 'Buscar etiqueta...',
    'non_selected_header': null,
    'selected_header': null,
    'limit': -1,
    'limit_reached': function () {},
});


//var simplemde = new SimpleMDE({
    //autoDownloadFontAwesome:false,
    //spellChecker:false,
    //element: document.getElementById("description")
//});
var simplemde = new SimpleMDE({
	autofocus: true,
	/*autosave: {
		enabled: true,
		uniqueId: "MyUniqueID",
		delay: 10000,
	},*/
    showIcons: ["code", "table"],
    autoDownloadFontAwesome:false,
    spellChecker:false,
    element: document.getElementById("articleBody")
});
