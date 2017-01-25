/*
 * JQuery MFupload v1.10
 *
 * Copyright 2011, Gianrocco Giaquinta
 * http://www.jscripts.info/
 *
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 */

(function( $ ){

	var settings;
	
    var methods = {
        completed: function(){},
        start: function(){},
        loaded: function(){},
		progress: function(){},
        error: function(){},
		
		upload_old: function(ctl, iframe){
			
			settings.init();
			
			var fnam = ctl.attr('value').replace(/^.*[\\\/]/, '')
						
			var xload = {fileno:-1, filename: fnam, perc:0, sent: 0, total: 0};						
			settings.start(xload);
			
			$('<iframe id="'+iframe+'" name="'+iframe+'" src="#" style="display:none"></iframe>').appendTo('body');
			
			$('#'+iframe).bind('load', function() {
				
				var tmp = $('#'+iframe).contents().find('body').html();
				var result = jQuery.parseJSON( tmp );
				result.fileno=-1;
				settings.loaded( result );
				settings.completed();
												
				$('#'+iframe).unbind('load');
				$('#'+iframe).remove();
			});
									
			ctl.parent().submit();	
		},
				
		upload: function(files){
					
			nfiles = files.length;
			cfiles = nfiles;
			
			settings.init();
			
			if (typeof files !== "undefined") {
                for (var i=0; i< nfiles; i++) {
                    
					var ext = files[i].name.toLowerCase().split(/\./);
            		ext = ext[ext.length -1];
										
					if ( settings.type && settings.type.indexOf(ext) == -1 ) {
						var err = { err_no: 1, err_des: "File type error", fileno: i, filename: files[i].name };
						settings.error(err);
						cfiles --; if ( cfiles == 0) settings.completed(); 
			
					} else if ( settings.maxsize>0 && files[i].size > settings.maxsize*1048576 ) {
						var err = { err_no: 2, err_des: "File size error", fileno: i, filename: files[i].name };
						settings.error(err);
						cfiles --; if ( cfiles == 0) settings.completed(); 
						
					} else			
					{
						
					var xload = {fileno:i, filename: files[i].name, perc:0, sent: 0, total: 0};						
					settings.start(xload);			

					try   
   						{ 
   							// Firefox, Opera 8.0+, Safari  
   							var xhr = new XMLHttpRequest();
   						}  
  						catch (e)    
   						{
							alert("Your browser does not support AJAX!");      
    						return false;      
    					}    
   					
					xhr.open("POST", settings.post_upload, true);
					xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            		xhr.upload.filenumb = i;
					xhr.filenumb = i;
					xhr.upload.filename = files[i].name;
										
					xhr.upload.addEventListener("progress", function (e) {
                	
					 if (e.lengthComputable) {
						 
						var loaded = Math.ceil((e.loaded / e.total) * 100);
                    	var xload = { fileno:this.filenumb, filename: this.filename, perc: loaded, sent: e.loaded, total: e.total };
						settings.progress(xload);
						
					 }
            		}, false);
            
					xhr.addEventListener("load", function (e) {
                		
						var result = jQuery.parseJSON(e.target.responseText);
						result.fileno = this.filenumb;
						settings.loaded(result);
						cfiles --; if ( cfiles == 0) settings.completed(); 
												
					}, false);
															
					var nef = new FormData();
					nef.append("folder", settings.folder);
					nef.append("file_element", settings.file_elem);
					nef.append("udata", settings.user_data);
				    nef.append(settings.file_elem + '[]', files[i]);
            		xhr.send(nef);
					
					}
								
					
                }
            } else {
                alert("no support");
            }
					
			
		}
	}

	$.fn.mfupload = function(opt) {
        
        settings = {
			
            'init'        : methods.init,
            'start'       : methods.start,
            'loaded'      : methods.loaded,
			'progress'    : methods.progress,
			'completed'	  : methods.completed,			
            'ini_text'	  : 'drag files to here',
            'over_text'   : 'drop files',
            'over_col'	  : 'white',
			'over_bkcol'  : 'green',
			'post_upload' : './upload.php',
            'maxsize'     : '1', //default 1MB
			'type'		  : '',
			'folder'	  : './',
			'user_data'	  : '',
			'file_elem'	  : ''
        };
		
	if(opt) $.extend(settings, opt);
	
	  this.each(function()	{
	  if ( !((window.location != window.parent.location) ? true : false) )
	  {
		  
		settings.file_elem = "mf_file_"+$(this).attr("id");
		settings.iframe = "mf_iframe_"+$(this).attr("id");
				
		$(this).append('<div class="mf_upload_m"><form method="post" enctype="multipart/form-data" class="file" action="'+settings.post_upload+'" target="'+settings.iframe+'"><input type="file" class="file" name="'+settings.file_elem+'" id="'+settings.file_elem+'" multiple /><input type="hidden" name="folder" value="'+settings.folder+'" /><input type="hidden" name="file_element" value="'+settings.file_elem+'" /></form><div class="mf_upload_ins"></div></div>');
		
		$(this).find(".mf_upload_m").css({
			'position': 'relative',
			'margin': 0,
			'padding':0,
			'width': 'inherit',
			'height' : 'inherit'
	  	});
					
		$(this).find(".file").css({
			'position': 'relative',
			'text-align': 'right',
			'-moz-opacity': '0',
			'filter': 'alpha(opacity: 0)',
			'opacity': '0',
			'z-index': '2',
			'width': '100%',
			'height' : '100%',
			top:0, left:0
	  	});
		
		$(this).find(".mf_upload_ins").css({
			'position': 'absolute',
			'text-align': 'center',
			'z-index': '1',
			'width': '100%',
			'height' : '100%',
			'top':0,
			'left':0,	 
	  	});
						
		
		$(this).find('.mf_upload_ins').empty().html(settings.ini_text);
		
		$(this).bind({
                dragleave: function (e) {
                    e.preventDefault();
					$(this).find(".mf_upload_ins").css('color', 'inherit');
					$(this).find(".mf_upload_ins").css('background-color', 'inherit').html(settings.ini_text);
                },
                drop: function (e) {
                    e.preventDefault();
					$(this).find(".mf_upload_ins").css('color', 'inherit');
					$(this).find(".mf_upload_ins").css('background-color', 'inherit' ).html(settings.ini_text);
					
					var obj = e.originalEvent;
					if (obj.dataTransfer.files)
						methods.upload(obj.dataTransfer.files);
					else
						alert("Drag and Drop not supported by your browser.");					
								
				},
                dragover: function (e) {
					e.preventDefault();
					$(this).find(".mf_upload_ins").css('color', settings.over_col);
					$(this).find(".mf_upload_ins").css('background-color', settings.over_bkcol);					
					$(this).find(".mf_upload_ins").html(settings.over_text);                
					
				}
         });
		 
		 $(this).find('input[type=file]').bind('change', function(e){
			e.preventDefault();
			
			if(e.target.files) methods.upload(e.target.files);
			else methods.upload_old( $(this), settings.iframe );
  			
         });
		 	 
	  }
	  });	
		
	}


})( jQuery );