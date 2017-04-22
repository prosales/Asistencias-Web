app.service('reportesService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getVentas = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'reporte_ventas',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };
    
    this.getAsistencias = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'reporte_asistencias',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.getMarcajes = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'reporte_marcajes',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

}]);