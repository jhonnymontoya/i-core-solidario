/**
 * devuelve el dígito de verificación de un nit mediante ajax
 * @param  {[type]} nit [description]
 * @return {[type]}     [description]
 */
function digitoVerificacion(nit, url, destino)
{
	//var url = "{{ url('api/tercero/dv') }}";
	var data = "numeroIdentificacion=" + nit;

	if(nit.length <= 0)
	{
		destino.html("0");
		return;
	}

	$.get(url, data, function(result)
	{
		destino.html(result.digitoVerificacion);
	});
}

jQuery.fn.extend({

	/**
	 * Hace foco en un control de formulario especifico
	 * @return {[type]} [description]
	 */
	enfocar: function()
	{
		this.focus();
		this.val(this.val());
	},

	selectAjax: function(urlAjax, argumentos){
		argumentos = argumentos || {id: false, entidad: ''};
		this.select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: urlAjax,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term, // search term
						page: params.page,
						entidad: argumentos.entidad
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});
		id = argumentos.id;
		if(id)
		{
			var self = this;
			$.ajax({url: urlAjax, dataType: 'json', data: {id: id}}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo(self);
					self.val(element.id).trigger("change");
				}
			});
		}
	},

	formularioCrear: function(url){
		this.keydown(function(event) {
			if(event.altKey && event.keyCode == 78) { 
				window.location.href = url;
				event.preventDefault();
			}
		});
	},

	formatoMoneda: function(valor) {
		var input = $('<input/>').attr({type: 'text'});
		input.maskMoney('mask');
		var resp = input.maskMoney('maskvalue', valor);
		return resp;
	}

});