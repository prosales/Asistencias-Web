app.controller('SupervisoresController', ['$scope', '$rootScope', '$modal', '$timeout', 'supervisoresService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , supervisoresService, ngTableParams, $filter, toaster) {
  
    $scope.data = [];
    $scope.table = [];
    $scope.sucursales = [];
    $rootScope.pageTitle = "Supervisores";
    $scope.settings = {
        singular: 'Supervisor',
        plural: 'Supervisores',
        modal: 'Crear Supervisor',
        accion: 'Guardar'
    }

    function actualizar_datos()
    {
        supervisoresService.getData('GET', {}).then(function(dataResponse) {
                $scope.data = dataResponse.data.records;
                $scope.table = dataResponse.data.records;
                $scope.tableParams.reload();
        });
    }

    actualizar_datos();

    $scope.tableParams = new ngTableParams({
        page: 1,
        count: 10,
        sorting: {
            id: 'asc'
        }
    }, {
        filterDelay: 50,
        total: $scope.table.length,
        getData: function($defer, params) {
            var searchStr = params.filter().search;
            if (searchStr) {

                searchStr = searchStr.toLowerCase();
                $scope.table = $scope.data.filter(function(item) {
                    return item.nombre.toLowerCase().indexOf(searchStr) > -1 || item.telefono.toLowerCase().indexOf(searchStr) > -1 || item.usuario.toLowerCase().indexOf(searchStr) > -1;
                });

            } else {
                $scope.table = $scope.data;
            }
            $scope.table = params.sorting() ? $filter('orderBy')($scope.table, params.orderBy()) : $scope.table;
            $defer.resolve($scope.table.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
    });

    var modalInstance;

    $scope.createItem = function()
    {
        $scope.item = {};
        $scope.settings.accion = "Crear";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-supervisor.html',
            scope: $scope,
            size: 'lg'
      });
    }

    $scope.editItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Editar Supervisor";
        $scope.settings.accion = "Editar";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-supervisor.html',
            scope: $scope,
            size: 'lg'
        })
    }

    $scope.deleteItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Eliminar Supervisor";
        $scope.settings.accion = "Eliminar";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-eliminar.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.bajaItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Baja Supervisor";
        $scope.settings.accion = "Dar baja";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-baja.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.altaItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Alta Supervisor";
        $scope.settings.accion = "Dar alta";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-alta.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.hide = function()
    {
        modalInstance.close();
    }

    $scope.saveItem = function()
    {
        if( $scope.settings.accion == "Crear" )
        {
            supervisoresService.create( $scope.item ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Editar" )
        {
            supervisoresService.update( $scope.item ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Eliminar" )
        {
            supervisoresService.delete( $scope.item.id ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Dar alta" )
        {
            var data = {
                id: $scope.item.id,
                estado: 1
            }
            supervisoresService.update( data ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Dar baja" )
        {
            var data = {
                id: $scope.item.id,
                estado: 0
            }
            supervisoresService.update( data ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }

    }

}]);