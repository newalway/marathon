
function selectFileWithCKFinder( elementId, thumbnailId ) {
	thumbnailId = typeof thumbnailId !== 'undefined' ? thumbnailId : false;

	CKFinder.popup( {
		width: 800,
		height: 600,
		chooseFiles: true,
		rememberLastFolder: true,
    	startupFolderExpanded: true,
		onInit: function( finder ) {
			finder.on( 'files:choose', function( evt ) {
				var file = evt.data.files.first();
				// var files = evt.data.files.toArray();

				var output = document.getElementById( elementId );
				output.value = file.getUrl();

				if(thumbnailId){

					var fileThumbUrl = finder.request( 'file:getThumb', { file: file } ),
						thumbnail_image = document.getElementById( thumbnailId );

					// // https://docs.ckeditor.com/ckfinder/ckfinder3/#!/api/CKFinder.Models.Folder
					// var folder = file.get('folder'),
					// type = folder.get('resourceType'),
					// currentFolder = folder.getPath(),
					// hash = folder.getHash(),
					// fileName = file.get('name'),
					// fileDate = file.get('date'),
					// fileSize = file.get('size'),
					// fileThumbUrl = "/ckfinder/core/connector/php/connector.php?command=Thumbnail&lang=en"+"&type="+type+"&currentFolder="+currentFolder+"&hash="+hash+"&fileName="+fileName+"&date="+fileDate+"&fileSize="+fileSize+"&size=150x150";

					thumbnail_image.src = fileThumbUrl;

					//folder
					// var folder = finder.request( 'folder:getActive' );
					//folder url
					// var folderUrl = folder.getUrl();
				}
			} );

			finder.on( 'file:choose:resizedImage', function( evt ) {
				var output = document.getElementById( elementId );
				output.value = evt.data.resizedUrl;
			} );

			// finder.on( 'app:ready', function ( evt ) {
            //      finder.request( 'dialog:info', {
            //          msg: 'Welcome to CKFinder!'
            //      } );
            // } );
		}
	} );
}

/*
function selectFileWithCKFinder( elementId ) {
	CKFinder.modal( {
		width: 800,
		height: 600,
		chooseFiles: true,
		rememberLastFolder: true,
    	startupFolderExpanded: true,
		onInit: function( finder ) {
			finder.on( 'files:choose', function( evt ) {
				var file = evt.data.files.first();
				var output = document.getElementById( elementId );
				output.value = file.getUrl();
			} );

			finder.on( 'file:choose:resizedImage', function( evt ) {
				var output = document.getElementById( elementId );
				output.value = evt.data.resizedUrl;
			} );
		}
	} );
}
*/
