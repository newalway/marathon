var contactApp = angular.module('contactApp',[]);
contactApp.factory('contact_data', ['$http', function contact_data($http){
  return {
    contact_create: function(dataObj){
      return $http({
        method:'POST',
        url:Routing.generate('contact_create', true),
        data: $.param(dataObj),
        headers: {'Content-Type':'application/x-www-form-urlencoded'
      } });
    }
  };
}]);

contactApp.controller('contactController', ['$scope', 'contact_data', function($scope, contact_data){

  $scope.processForm = function(){
    $scope.setDataLoading(true);
    $scope.formData._token = angular.element('#contact__token').val();
    contact_data.contact_create($scope.formData)
    .then(function onSuccess(response) {
      var data = response.data;
      if(!data.success){
        $scope.message = data.message;
        $scope.errors = data.errors;
        $scope.success = data.success;
      }else{
        $scope.message = data.message;
        //reset data
        $scope.formData = {};
        $scope.errors = {};
        $scope.success = {};
      }
      $scope.setDataLoading(false);

    }).catch(function onError(response) {

    });
  };

  $scope.getDataLoading = function() {
    return $scope.data_loaded;
  };
  $scope.setDataLoading = function(value) {
    $scope.data_loaded = value;
  };

  $scope.formData = {};
  $scope.errors = {};
  $scope.message = undefined;
  $scope.setDataLoading(false);

}]);
