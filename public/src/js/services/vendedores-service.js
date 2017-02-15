app.service('vendedoresService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'vendedores',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.delete = function(id) {
        return $http.delete(APP.api + 'vendedores/' + id);
    };

    this.update = function(parametros) {
        return $http.put(APP.api + 'vendedores/' + parametros.id, parametros);
    };

    this.create = function(parametros) {
        return $http.post(APP.api + 'vendedores', parametros);
    };

    this.createTelefono = function(parametros)
    {
        return $http.post(APP.api + 'telefonos', parametros);
    };

    this.deleteTelefono = function(parametros)
    {
        return $http.get(APP.api + 'telefonos/' + parametros.id);
    }

}]);