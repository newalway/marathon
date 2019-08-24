var app = angular.module('searchApp',[]);

app.factory('search_data', ['$http', function search_data($http){
  return {
    search_result: function(q){
      return $http({
        method:'GET',
        url: Routing.generate('search_api', {q:q}),
        headers: {
          'Content-Type':'application/x-www-form-urlencoded'
        }
      });
    }
  };
}]);

app.controller('searchController',['$scope', '$sce', 'search_data', function($scope, $sce, search_data){
  $scope.itemsPerPage = 3;

  $scope.blogCurrentPage = 0;
  $scope.newsCurrentPage = 0;
  $scope.productCurrentPage = 0;
  $scope.salonCurrentPage = 0;
  $scope.styleCurrentPage = 0;
  $scope.menu = 1;

  $scope.init = function(q){
    $scope.getSearchData(q);
  }

  $scope.clickMenu = function(section){
    console.log($scope.menu);
  }

  $scope.current_page = function(data,current_page){
    var offset = current_page*$scope.itemsPerPage;
    return data.slice(offset, offset + $scope.itemsPerPage);
  }

  $scope.loadMore = function(section,data) {
    if(section === 'blog'){
      $scope.blogCurrentPage++;
      var newItems = $scope.current_page(data,$scope.blogCurrentPage);
      $scope.blogItems = $scope.blogItems.concat(newItems);
    }
    if(section === 'news'){
      $scope.newsCurrentPage++;
      var newItems = $scope.current_page(data,$scope.newsCurrentPage);
      $scope.newsItems = $scope.newsItems.concat(newItems);
    }
    if(section === 'product'){
      $scope.productCurrentPage++;
      var newItems = $scope.current_page(data,$scope.productCurrentPage);
      $scope.productItems = $scope.productItems.concat(newItems);
    }
    if(section === 'salon'){
      $scope.salonCurrentPage++;
      var newItems = $scope.current_page(data,$scope.salonCurrentPage);
      $scope.salonItems = $scope.salonItems.concat(newItems);
    }
    if(section === 'style'){
      $scope.styleCurrentPage++;
      var newItems = $scope.current_page(data,$scope.styleCurrentPage);
      $scope.styleItems = $scope.styleItems.concat(newItems);
    }

  }

  $scope.nextPageDisabledClass = function(section,total) {
    if(section === 'blog'){
      return $scope.blogCurrentPage === $scope.pageCount(total)-1 ? "hide" : "";
    }
    if(section === 'news'){
      return $scope.newsCurrentPage === $scope.pageCount(total)-1 ? "hide" : "";
    }
    if(section === 'product'){
      return $scope.productCurrentPage === $scope.pageCount(total)-1 ? "hide" : "";
    }
    if(section === 'style'){
      return $scope.styleCurrentPage === $scope.pageCount(total)-1 ? "hide" : "";
    }
    if(section === 'salon'){
      return $scope.salonCurrentPage === $scope.pageCount(total)-1 ? "hide" : "";
    }
  };

  $scope.pageCount = function(total) {
    return Math.ceil(total/$scope.itemsPerPage);
  };


  $scope.getSearchData = function(q){
    if(q){
      search_data.search_result(q).then(function(data){
        var res = data.data; //console.log(res);
        $scope.all_result = res.all_result;
        $scope.result = res.result;

        $scope.items = res.data;

        $scope.productItems = $scope.current_page(res.data.product,0);
        $scope.blogItems = $scope.current_page(res.data.blog,0);
        $scope.newsItems = $scope.current_page(res.data.news,0);
        $scope.salonItems = $scope.current_page(res.data.salon,0);
        $scope.styleItems = $scope.current_page(res.data.style,0);

      });
    }
  }

  //tab
  $scope.toggle = function (val){
    $scope.isVisible = val;
    $scope.isVisibleActive = true;
  };

  $scope.toggleClose = function (){
    $scope.isVisible = 0;
    $scope.isVisibleActive = false;
  };

}]);

app.filter('htmlToPlaintext', function() {
  return function(text) {
    return angular.element(text).text();
  }
});

app.directive('dotdotdot', function(){
  return {
      restrict: 'A',
      link: function(scope, element, attributes) {
          element.dotdotdot({watch: true, wrap: 'letter'});
      }
  }
});

app.filter('slugFilter', [function() {
  return function(string) {
    var slug = '-';
    if(string) {
      slug = string.toLowerCase().trim();
      //Remove inner-word punctuation
      slug = slug.replace(/[\'"‘’“”]+/g, '');
      // replace multiple spaces or hyphens with a single hyphen
      slug = slug.replace(/[\s-]+/g, '-');
    }
    return slug;
  };
}])
