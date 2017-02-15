app.service('dashboardService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getAsistencias = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'asistencias/lista',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.getReportes = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'mensajes/lista',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

}]);