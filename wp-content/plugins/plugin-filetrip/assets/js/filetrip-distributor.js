function process_backup_transfer(e){var i=dist_tasks_options.registeredChannels[dist_tasks_options.channel],n=new Date;void 0===i&&(jQuery("#filetrip-transfer-cards").append("<h2>  This backup archive doesn't has any forwarding channels selected</h2>"),jQuery("#filetrip-transfer-cards").append("<p> Backup ("+n.toDateString()+")</p>"));var t={channel_name:i.channel_name,mime:"application/zip",channel_key:i.channel_key,active:i.active,descriptive_progress:dist_tasks_options.file_size_friendly,default_dest:i.destination?i.destination:"",channel_icon:i.channel_icon,card_id:i.channel_key};jQuery(e(t)).appendTo("#filetrip-transfer-cards").show("slow"),i.channel_action_url=i.channel_action_url+"_backup",mediaEmulator={id:"",file_size_friendly:dist_tasks_options.file_size_friendly},new FiletripBackendUploader(dist_tasks_options,i,mediaEmulator).sendFile().then(function(e){console.log("Async code terminated successfully.")}).catch(function(e){console.log("Handle rejected promise ("+e+") here.")})}function process_media_library(e){for(var i in dist_tasks_options.mediaList){var n=dist_tasks_options.mediaList[i],t=dist_tasks_options.registeredChannels[n.forwarders.single_channel],s={channel_name:t.channel_name,channel_key:t.channel_key,file_name:n.title,mime:n.mime,active:t.active,descriptive_progress:n.file_size_friendly,default_dest:t.destination?t.destination:"",channel_icon:t.channel_icon,card_id:t.channel_key+n.id};jQuery(e(s)).appendTo("#filetrip-transfer-cards").show("slow"),new FiletripBackendUploader(dist_tasks_options,t,n).sendFile().then(function(e){console.log("Async code terminated successfully.")}).catch(function(e){console.log("Handle rejected promise ("+e+") here.")})}}function process_media_forwarders(e){for(var i in dist_tasks_options.mediaList){var n=dist_tasks_options.mediaList[i];for(var t in n.forwarders.length<=0&&(jQuery("#filetrip-transfer-cards").append("<h2> ["+n.id+"]: This upload doesn't has any forwarding channels selected</h2>"),jQuery("#filetrip-transfer-cards").append("<p>"+n.title+"</p>")),n.forwarders){var s=n.forwarders[t],a={channel_name:s.channel_name,file_name:n.title,channel_key:s.channel_key,mime:n.mime,active:s.active,descriptive_progress:n.file_size_friendly,default_dest:s.destination?s.destination:"",channel_icon:s.channel_icon,card_id:s.channel_key+n.id};jQuery(e(a)).appendTo("#filetrip-transfer-cards").show("slow"),new FiletripBackendUploader(dist_tasks_options,s,n).sendFile().then(function(e){console.log("Async code terminated successfully.")}).catch(function(e){console.log("Handle rejected promise ("+e+") here.")})}}}function prepare_help_tip(t){if(t.fn.pointer){var s=[];t(".help_tip").on("click",function(e){e.preventDefault();var i="<h3>"+(t(this).attr("data-title")||"&nbsp;")+"</h3><p>"+t(this).attr("data-tip")+"<p>";if(t(".wp-pointer").is(":visible")){var n=t(".wp-pointer:visible").attr("id").replace("wp-pointer-","");n=parseInt(n),"object"==typeof s[n]&&t(s[n]).pointer("toggle")}t(this).pointer({content:i,position:"bottom",show:function(e,i){var n=i.pointer[0].id.replace("wp-pointer-","");n=parseInt(n),s[n]=this,i.pointer[0].className=i.pointer[0].className.replace(/\s+(wp-pointer-[^\s'"])/,"$1"),i.pointer.show(),i.opened()}}).pointer("open").pointer("repoint")})}}function bytesToSize(e){if(0==e)return"0 Bytes";var i=parseInt(Math.floor(Math.log(e)/Math.log(1024)));return Math.round(e/Math.pow(1024,i),2)+" "+["Bytes","KB","MB","GB","TB"][i]}function FiletripFilter(e){"all"!=e?(jQuery(".filetrip-transfer-item").hide(),jQuery("."+e).css("display","")):jQuery(".filetrip-transfer-item").css("display","")}jQuery.noConflict(),jQuery(document).ready(function(e){prepare_help_tip(jQuery);var i=wp.template("upload-card-single");console.log(dist_tasks_options),"upload-forwarder"==dist_tasks_options.source?process_media_forwarders(i):"media-library"==dist_tasks_options.source?process_media_library(i):"filetrip-backup"==dist_tasks_options.source&&process_backup_transfer(i)}),function(){jQuery(function(){return jQuery("[data-toggle]").on("click",function(){var e;return e=jQuery(this).addClass("active").attr("data-toggle"),jQuery(this).siblings("[data-toggle]").removeClass("active"),jQuery(".surveys").removeClass("grid list").addClass(e)})})}.call(this);