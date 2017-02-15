app.controller('DashboardController', ['$scope', '$rootScope', '$modal', '$timeout', 'dashboardService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , dashboardService, ngTableParams, $filter, toaster) {
  
    $scope.data = [];
    $scope.table = [];
    $rootScope.pageTitle = "Dashboard";
    $scope.settings = {
        singular: 'Dashboard',
        plural: 'Dashboard',
        modal: '',
        accion: ''
    }

    
	$scope.entradas_pdv = 0;
	$scope.salidas_almuerzo = 0;
	$scope.entradas_almuerzo = 0;
	$scope.salidas_pdv = 0;

	function actualizar_datos()
    {
        dashboardService.getAsistencias('GET', {fecha_inicio: "", fecha_fin: ""}).then(function(dataResponse) {

        		$scope.entradas_pdv = dataResponse.data.entradas_pdv;
				$scope.salidas_almuerzo = dataResponse.data.salidas_almuerzo;
				$scope.entradas_almuerzo = dataResponse.data.entradas_almuerzo;
				$scope.salidas_pdv = dataResponse.data.salidas_pdv;

                $scope.data = dataResponse.data.records;
                $scope.table = dataResponse.data.records;
                $scope.tableParams.reload();
        });

        dashboardService.getReportes('GET', {fecha_inicio: "", fecha_fin: ""}).then(function(dataResponse) {

                $scope.dataReportes = dataResponse.data.records;
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
                    return item.numero.toLowerCase().indexOf(searchStr) > -1 || item.nombre_vendedor.toLowerCase().indexOf(searchStr) > -1;
                });

            } else {
                $scope.table = $scope.data;
            }
            $scope.table = params.sorting() ? $filter('orderBy')($scope.table, params.orderBy()) : $scope.table;
            $defer.resolve($scope.table.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
    });

}]);