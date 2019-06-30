(function($, AdminLTE){
	var my_skins = [
		"skin-blue",
		"skin-black",
		"skin-red",
		"skin-yellow",
		"skin-purple",
		"skin-green",
		"skin-blue-light",
		"skin-black-light",
		"skin-red-light",
		"skin-yellow-light",
		"skin-purple-light",
		"skin-green-light"
	];
	var my_class = "";

	setup();

	/**
	* Replaces the old skin with the new skin
	* @param String cls the new skin class
	* @returns Boolean false to prevent link's default action
	*/
	function change_skin(cls) {
		//En esta función guardar propiedades
		$.each(my_skins, function(i){
			$("body").removeClass(my_skins[i]);
		});
		$("body").addClass(cls);
		store('skin', cls);
		return false;
	}

	/**
	* Store a new settings in the browser
	*
	* @param String name Name of the setting
	* @param String val Value of the setting
	* @returns void
	*/
	function store(name, val) {
		if(typeof (Storage) !== "undefined") {
			localStorage.setItem(name, val);
		}
		else {
			window.alert('Por favor use un navegador moderno para usar este estilo!');
		}
	}

	/**
	* Get a prestored setting
	*
	* @param String name Name of of the setting
	* @returns String The value of the setting | null
	*/
	function get(name) {
		if(typeof(Storage) !== "undefined") {
			return localStorage.getItem(name);
		}
		else {
			window.alert('Por favor use un navegador moderno para usar este estilo!');
		}
	}

	function saveUiConfig() {
		//var clase = $("body").hasClass("sidebar-collapse") ? "sidebar-collapse" : null;
		var clase = my_class;
		var tema = "skin-blue";
		if($("body").hasClass("skin-blue")) tema = "skin-blue";
		if($("body").hasClass("skin-black")) tema = "skin-black";
		if($("body").hasClass("skin-red")) tema = "skin-red";
		if($("body").hasClass("skin-yellow")) tema = "skin-yellow";
		if($("body").hasClass("skin-purple")) tema = "skin-purple";
		if($("body").hasClass("skin-green")) tema = "skin-green";
		if($("body").hasClass("skin-blue-light")) tema = "skin-blue-light";
		if($("body").hasClass("skin-black-light")) tema = "skin-black-light";
		if($("body").hasClass("skin-red-light")) tema = "skin-red-light";
		if($("body").hasClass("skin-yellow-light")) tema = "skin-yellow-light";
		if($("body").hasClass("skin-purple-light")) tema = "skin-purple-light";
		if($("body").hasClass("skin-green-light")) tema = "skin-green-light";
		var data = {
			clase: clase,
			tema: tema
		};
		$.ajax({
			type: "GET",
			url: "/usuario/uiConfiguracion",
			data: data
		});
	}

	/**
	* Retrieve default settings and apply them to the template
	*
	* @returns void
	*/
	function setup() {
		var tmp = get('skin');
		if(tmp && $.inArray(tmp, my_skins))change_skin(tmp);

		//Add the change skin listener
		$("[data-skin]").on('click', function(e) {
			my_class = $("body").hasClass("sidebar-collapse") ? "sidebar-collapse" : null;
			if($(this).hasClass('knob'))return;
			e.preventDefault();
			change_skin($(this).data('skin'));
			saveUiConfig();
		});

		$(".sidebar-toggle").on('click', function(e) {
			my_class = $("body").hasClass("sidebar-collapse") ? null : "sidebar-collapse";
			saveUiConfig();
		});
	}

})(jQuery, $.AdminLTE);