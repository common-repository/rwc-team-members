/**
 * Admin JavaScript
 *
 * This JavaScript file contains scripts specific to the admin section of the
 * "RWC Team Members" plugin. These scripts enhance the functionality and
 * user experience of the plugin's backend pages, providing interactive features
 * and dynamic behavior for administrators.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
jQuery(document).ready(function($) {
    "use strict";
	// Hide span tag to show on mouse over
	$(".team_name span").css("display","none");
	
	// Show span tag on mouse over
	$(".team_name").mouseover(function(){
		var linkid = $(this).attr("id");
		$("td#" + linkid + " span").css("display","inline-block");
	});
	
	// Hide span tag on mouse out
	$(".team_name").mouseout(function(){
		var linkid = $(this).attr("id");
		$("td#" + linkid + " span").css("display","none");
	});

	// DIsplay Global Settings
	$('#rwctm-tabs').tabs();	// Tabs
	$('#rwctm-tabs').css('visibility','visible');

	// check default font is checked or not
	if($("#default_font").is(':checked')) {
		$("#user_choice_font").css("display","none");
	} else {
		$("#user_choice_font").css("display","inline-block");
	}

	// show or hide custom font on default font selection
	$("#default_font").click(function() {
		var dfont = $(this).is(':checked');
		if(dfont) {
			$("#user_choice_font").slideUp("slow");
		} else {
			$("#user_choice_font").slideDown("slow");
		}
	});

	// custom css editor
	$(function(){
        if( $('#rwctm-fancy-textarea').length ) {
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
					autoRefresh:true,
                    mode: 'css',
                }
            );
            var editor = wp.codeEditor.initialize( $('#rwctm-fancy-textarea'), editorSettings );
			setTimeout(function() {
				editor.codeMirrorInstance.refresh();
			},1);
        }
    });

	// check WP Post Editor is checked or not
	if($("#wp_post_editor").is(':checked')) {
		$("#htmleditor").css("display","block");
	} else {
		$("#htmleditor").css("display","none");
	}

	// show or hide Mirror Editor on WP Post Editor selection
	$("#wp_post_editor").click(function() {
		var wpeditor = $(this).is(':checked');
		if(wpeditor) {
			$("#htmleditor").slideDown("slow");
		} else {
			$("#htmleditor").slideUp("slow");
		}
	});

	// Create a new team from selected template
	$('.activate_team').on('click', function() {
		$.rwctmactivatetemp();
	});

	// Delete only a selected team
	$('.rwctm_remove').on('click', function() {
		$.rwctmdeleteteam();
	});

	// Switch into a selected teplates
	$('.rwctm_template').on('change', function() {
		$.rwctmtemplate();
	});

	// Duplicate a selected team
	$('.duplicate_team').on('click', function() {
		$.rwctmcopyteams();
	});

	// Regenerating Shortcode IDs
	$('.regen_shortcode').on('click', function() {
		$.rwctmregenshortcode();
	});

	// Edit activities of a selected team
	$('.rwctm_activity').on('click', function() {
		$.rwctmeditactivity();
	});

	// Preview a selected team
	$('.view_team').on('click', function() {
		$.rwctmviewteam();
	});

	// Edit team member, member info and column settings
	$('.process_team').on('click', function() {
		$.rwctmsetupmember();
	});

    $('.demo_link').on('click', function(e) {
        e.preventDefault();

        var imageUrl = $(this).attr('href');
		if (imageUrl) {
			var modalHtml = '<div id="rwctm-modal-container">';
			modalHtml += '<div id="rwctm-modal-content">';
			modalHtml += '<img src="' + imageUrl + '" alt="Demo Image">';
			modalHtml += '</div>';
			modalHtml += '</div>';

			$('body').append(modalHtml);

			$('#rwctm-modal-container').fadeIn('slow');

			$('#rwctm-modal-container').on('click', function() {
				$(this).fadeOut('slow', function() {
                    $(this).remove();
                });
			});
		}
    });
});

/* copy shorcode on click */
function copyShortcode(id) {
	"use strict";
	var id;
	var copyText = document.getElementById("rwctmInput-" + id);
	copyText.select();
	document.execCommand("copy");

	var tooltip = document.getElementById("rwctmTooltip-" + id);
	tooltip.innerHTML = "Copied!";
}

function outFunc() {
	"use strict";
	var id;
	var tooltip = document.getElementById("rwctmTooltip-" + id);
	tooltip.innerHTML = "Click to Copy Shortcode!";
}

( function($) {
	"use strict";
	// Activate a template for an Existed Team
	$.rwctmactivatetemp = function () {
		var tcount;
		$('body').on('click', '.activate_team', function() {
			tcount = $(this).attr('data-count');
			$.ajax({
				type: 'POST',
				url: rwctmajax.ajaxurl,
				data: {
					action: 'rwctm_activate_template',		// The function that will activate a template
					tempcount: tcount,
					nonce: rwctmajax.nonce
				},
				beforeSend: function(){
					// Show image container
					$("#rwctm-loading-image").css("display","inline-block");
				},
				success:function(data, textStatus, XMLHttpRequest){
					alert('Successfully Activated!');
					window.location.reload();
				},
				complete:function(){
					// Hide image container
					$("#rwctm-loading-image").hide();
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
		});
	};
	// Delete only a selected team after confirmation
	$.rwctmdeleteteam = function (e) {
		var selector;
		$('body').on('click', '.rwctm_remove', function() {
			selector = $(this).attr('data-id');
			var answer = confirm ("Are you sure you want to delete?");
			if (answer === true) {
				$.ajax({
					type: 'POST',
					url: rwctmajax.ajaxurl,
					data: {
						action: 'rwctm_delete_selected_team',			// The function that will delete the team
						teamname: selector,
						nonce: rwctmajax.nonce
					},
					beforeSend: function(){
						// Show image container
						$("#rwctm-loading-image").css("display","inline-block");
					},
					success:function(data, textStatus, XMLHttpRequest){
						var linkid = '#rwctm_' + selector;
						$(linkid).remove();
						$(linkid).append(data);
						window.location.reload();
					},
					complete:function(){
						// Hide image container
						$("#rwctm-loading-image").hide();
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
				e.preventDefault();
			} else{
				window.location.reload();
			}
		});
	};
	// Create Team Templates
	$.rwctmtemplate = function () {
		var tname;
		$('body').on('click', '.rwctm_template', function() {
			tname = $(this).attr('data-id');
			var answer = confirm ("Are you sure you want to setup this template?");
			if (answer === true) {
				$.ajax({
					type: 'POST',
					url: rwctmajax.ajaxurl,
					data: {
						action: 'rwctm_setup_selected_template',		// function that will create a template
						teamname: tname,
						template: this.value,
						nonce: rwctmajax.nonce
					},
					beforeSend: function(){
						// Show image container
						$("#rwctm-loading-image").css("display","inline-block");
					},
					success:function(data, textStatus, XMLHttpRequest){
						alert('The template has been successfully setup!');
						window.location.reload();
					},
					complete:function(){
						// Hide image container
						$("#rwctm-loading-image").hide();
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
			} else{
				window.location.reload();
			}
		});
	};
	// Copy Pricing Table
	$.rwctmcopyteams = function () {
		var tname;
		var mcount;
		$('body').on('click', '.duplicate_team', function() {
			tname = $(this).attr('data-id');
			mcount = $(this).attr('data-count');
			var answer = confirm ("Are you sure you want to duplicate this team?");
			if (answer === true) {
				$.ajax({
					type: 'POST',
					url: rwctmajax.ajaxurl,
					data: {
						action: 'rwctm_copy_existed_team',		// The function that will copy a team
						teamname: tname,
						membcount: mcount,
						nonce: rwctmajax.nonce
					},
					beforeSend: function(){
						// Show image container
						$("#rwctm-loading-image").css("display","inline-block");
					},
					success:function(data, textStatus, XMLHttpRequest){
						alert('Team Copied Successfully!');
						window.location.reload();
					},
					complete:function(){
						// Hide image container
						$("#rwctm-loading-image").hide();
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				});
			} else{
				window.location.reload();
			}
		});
	};
	// Regenerate Shortcode IDs
	$.rwctmregenshortcode = function () {
		var answer = confirm ("Are you sure you want to Regenerate Shortcode IDs?");
		if (answer === true) {
			jQuery.ajax({
				type: 'POST',
				url: rwctmajax.ajaxurl,
				data: {
					action: 'rwctm_regenerate_shortcode',
					nonce: rwctmajax.nonce
				},
				beforeSend: function(){
					// Show image container
					$("#rwctm-loading-image").css("display","inline-block");
				},
				success:function(data, textStatus, XMLHttpRequest){
					alert('Successfully Regenerated Shortcode ID!');
					window.location.reload();
				},
				complete:function(){
					// Hide image container
					$("#rwctm-loading-image").hide();
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
		} else{
			window.location.reload();
		}
	};
	// Edit existed team
	$.rwctmsetupmember = function () {
		var tname;
		var mcount;
		$('body').on('click', '.process_team', function() {
			tname = $(this).attr('data-id');
			mcount = $(this).attr('data-count');
			$.ajax({
				type: 'POST',
				url: rwctmajax.ajaxurl,
				data: {
					action: 'rwctm_process_team_members',			// The function that will edit a team
					teamname: tname,
					nonce: rwctmajax.nonce
				},
				beforeSend: function(){
					// Show image container
					$("#rwctm-loading-image").css("display","inline-block");
				},
				success:function(data, textStatus, XMLHttpRequest){
					var linkid = '#rwctm_list';
					$(linkid).html('');
					$("#new_team").hide();
					$('#rwctm-sidebar').hide();
					$('#rwctm-narration').hide();
					$(".team_list .subsubsub").hide();
					$('.regen_shortcode').hide();
					$(linkid).append(data);
					$('#form-messages').hide();
					$(".collapse").hide();
					$('#rwctm-tabs').tabs();	// Tabs
					// overwrite select
					$("#memb-section,#memb-status").selectize({
						maxItems: 3
					});
					$("#border_style,#image_size,#filter_align,#max_column,#max_coltab,#max_colmob,#thumbnail_align,#thumbcap_align").selectize();
					// activate accordion
					/* $(".column_container").accordion({
						collapsible: true,
						active:false
					});
					$(".member_advance").accordion({
						collapsible: true,
						active:false
					}); */

					$(".cs-inactive").hide();
					$(".common-settings").hide();
					$(".cs-inactive").click(function() {
						$(".common-settings").slideUp('slow');
						$(this).hide();
						$(".cs-active").show();
					});
					$(".cs-active").click(function() {
						$(".common-settings").slideDown('slow');
						$(this).hide();
						$(".cs-inactive").show();
					});

					// Collapse all member details
					$(".collapse").click(function() {
						$(".column_container").accordion({
							collapsible: true,
							active:false
						});
						$(".member_advance").accordion({
							collapsible: true,
							active:false
						});
						$(".common-settings").slideUp('slow');
						$(".cs-inactive").hide();
						$(".cs-active").show();
						$(".collapse").hide();
						$(".expand").show();
					});
		
					// Expand all member details
					$(".expand").click(function() {
						$(".column_container").accordion({
							collapsible: false,
							active:true
						});
						$(".member_advance").accordion({
							collapsible: false,
							active:true
						});
						$(".common-settings").slideDown('slow');
						$(".cs-active").hide();
						$(".cs-inactive").show();
						$(".expand").hide();
						$(".collapse").show();
					});
		
					// Enable/Disable team image
					var en_image = $(".enable_image input[type='checkbox']");
					var auto_margin = $(".img_margin_auto input[type='checkbox']");
					var img_size = $("select[id='image_size'] option:selected").val();
					$('#img-features').hide();
					$('#img-custom').hide();
					en_image.click(function() {
						if($("#enable_image").is(":checked")) {
							$('#img-features').slideDown("slow");
							if (img_size === 'custom') {
								$('#img-custom').slideDown("slow");
							} else {
								$('#img-custom').slideUp("slow");
							}
						} else {
							$('#img-features').slideUp("slow");
						}
					});
					if($("#enable_image").is(":checked")){
						$('#img-features').css("display","block");
						if (img_size === 'custom') {
							$('#img-custom').css("display","block");
						} else {
							$('#img-custom').css("display","none");
						}
					}
					$("select").change(function(){
						var img_size = $("select[id='image_size'] option:selected").val();
						if (img_size === 'custom') {
							$('#img-custom').slideDown("slow");
						} else {
							$('#img-custom').slideUp("slow");
						}
					});

					// Enable/Disable image margin
					auto_margin.click(function() {
						if($("#img_margin_auto").is(":checked")) {
							$('.image_margin').slideUp("slow");
						} else {
							$('.image_margin').slideDown("slow");
						}
					});
					if(jQuery("#img_margin_auto").is(":checked")){
						$('.image_margin').css("display","none");
					}

					// Show/Hide Slider options
					var slider_opt = $(".layout-style input[type='radio']");
					$('#slider-option').hide();
					slider_opt.click(function() {
						if($("#marked_layout_slide").is(":checked")) {
							$("#marked_layout_slide").parent('.labelexpanded').addClass('slider-before');
							$('#slider-option').slideDown("slow");
						} else {
							$("#marked_layout_slide").parent('.labelexpanded').removeClass('slider-before');
							$('#slider-option').slideUp("slow");
						}
					});
					if($("#marked_layout_slide").is(":checked")){
						$("#marked_layout_slide").parent('.labelexpanded').addClass('slider-before');
						$('#slider-option').css("display","block");
					}

					// Show/Hide Slider Autoplay Speed
					var sld_aplay = $(".slider_aplay input[type='checkbox']");
					$('#slider-apspeed').hide();
					sld_aplay.click(function() {
						if($("#slider_aplay").is(":checked")) {
							$('#slider-apspeed').slideDown("slow");
						} else {
							$('#slider-apspeed').slideUp("slow");
						}
					});
					if($("#slider_aplay").is(":checked")){
						$('#slider-apspeed').css("display","block");
					}

					// Show/Hide Pop-up direction
					var popup_dir = $(".layout-style input[type='radio']");
					$('.popup_choice').hide();
					popup_dir.click(function() {
						if($("#marked_layout_popup").is(":checked")) {
							$('.popup_choice').slideDown("slow");
						} else {
							$('.popup_choice').slideUp("slow");
						}
					});
					if($("#marked_layout_popup").is(":checked")){
						$('.popup_choice').css("display","block");
					}

					// Enable/Disable filter navigation
					var filt_anim = $(".enable_filter input[type='checkbox']");
					$('#filter-nav').hide();
					filt_anim.click(function() {
						if($("#enable_filter").is(":checked")) {
							$('#filter-nav').slideDown("slow");
						} else {
							$('#filter-nav').slideUp("slow");
						}
					});
					if($("#enable_filter").is(":checked")){
						$('#filter-nav').css("display","block");
					}
		
					if(mcount === 1) {
						$('#rwctm-1 #delMember').attr('id', 'delDisable');
						$('#rwctm-1 #hideMemb').attr('class', 'inactive');
					}
					$(".team_list").css("width","100%");
					$("#add_new_team h2").text("Edit RWC Team Member");
					$(".postbox-container").css("width","100%");
		
					// Make the column sortable
					$(function() {
						$('#sortable_column').sortable({
							cancel: ".column_container"
						});

						// Bind touchstart event to input elements
						$('#sortable_column input, #sortable_column textarea').on('touchstart', function(e) {
							e.preventDefault();
							$(this).focus();
							var val = $(this).val();
							$(this).val('').val(val); // Move cursor to end of input field
						});
					});
					$(".member_details").css("cursor","move");
		
					// Enable accordion for our team settings
					$('#accordion_advance').accordion({
						collapsible: true,
						active:false,
						heightStyle: "content"
					});
		
					for(var i = 1; i <= mcount; i++) {
						// Enable Accordion for Package Details
						$('#accordion' + i).accordion({
							collapsible: true,
							active:false,
							heightStyle: "content"
						});
						if($('#showMemb' + i + ' input').val() === 'hide') {
							$('#accordion' + i).hide();
						}
					}
		
					// Activating ColorPicker
					$('.team_bg').wpColorPicker();
					$('.ribbon_bg').wpColorPicker();
					$('.sm_fn_color').each(function () {
						var socialid = ($(this).attr('data-id'));
						$('#social_color_' + socialid).wpColorPicker();
					});
					$('.sm_hover_colo').wpColorPicker();
					$('.rwctm-color-picker').wpColorPicker();	// All Settings Option Color

		
					// Add New Column
					$('#addMember').click(function () {
						var num = $('.member_details').length,
						// preNum = Number(num-1),
						newNum  = Number(num + 1),
						// mbCount = parseInt($('#member-count').val()),
						// nmCount = Number(mbCount + 1),
						newElem = $('#rwctm-' + num).clone().attr('id', 'rwctm-' + newNum).fadeIn('slow');
						//alert(nmCount);
						newElem.find('#mcolumn' + num).attr('id', 'mcolumn' + newNum);
						newElem.find('#showMemb' + num).attr('id', 'showMemb' + newNum);
						newElem.find('#hideMemb' + num).attr('id', 'hideMemb' + newNum);
						newElem.find('#accordion' + num).attr('id', 'accordion' + newNum);
						newElem.find('#memb-section').attr('name', 'memb_department_['+newNum+']');
						newElem.find('#memb-status').attr('name', 'memb_designation['+newNum+']');
						newElem.find('#member-options' + num).attr('id', 'member-options' + newNum);
						newElem.find('#member-options' + newNum).attr('value', 'memberOptions' + newNum);
						newElem.find('#member-id').attr('value', newNum);
						newElem.find('#order-id').attr('value', newNum);
						$('#rwctm-' + num).after(newElem);
						$('.mtitle').focus();
		
						// Activate Accordion for Added Column
						$('#accordion' + newNum).accordion({
							collapsible: true,
							heightStyle: "content"
						});
						$('#mcolumn' + newNum).text('Team Member ' + newNum);
						$('#accordion' + newNum + ' .wp-color-result').remove();
		
						// Add Color Option for New Column
						$('.team_bg').wpColorPicker();
						$('.ribbon_bg').wpColorPicker();
						$('.mb_exp_color').wpColorPicker();
						$('.sm_hover_colo').wpColorPicker();

						$('#rwctm-1 #delDisable').attr('id', 'delMember');
						$('#rwctm-' + newNum + ' #delDisable').attr('id', 'delMember');
						$('.inactive').attr('class', 'column_hide');
					});
		
					$.rwctmMediaUploader();
		
					// Remove image
					$('body').on('click', '#remove_image', function() {
						var targetfield = $(this).parents('.element-input').find('#image_path');
						$(targetfield).val('');
						$(this).parents('#show_upload_preview').slideUp('slow');
					});
		
					// Delete a Column
					$('body').on('click', '#delMember', function() {
						var num = $('.member_details').length;
						var answer = confirm ("Are you sure you wish to remove this member? This cannot be undone!");
						if (answer) {
							$(this).parents('.member_details').slideUp('slow', function () {
								$(this).remove();
								if (num -1 === 1) {
									$('#delMember').attr('id', 'delDisable');
									$('.column_hide').attr('class', 'column_hide inactive');
									$(".member_details").css("cursor","auto");
									$("#sortable_column").sortable({ disabled: true });
								}
								$('#addMember').attr('disabled', false).prop('value', "New Column");
								var j = 1;
								for(i=1; i<=num; i++) {
									if($('#rwctm-' + i).length !== 0) {
										$('#rwctm-' + i).attr('id', 'rwctm-' + j);
										$("#mcolumn"+ i).text("Team Member " + j);
										$("#mcolumn"+ i).attr("id", "mcolumn"+ j);
										$("#hideMemb"+ i).attr("id", "hideMemb"+ j);
										$("#showMemb"+ i).attr("id", "showMemb"+ j);
										$('#accordion' + i).attr('id', 'accordion' + j);
										j++;
									}
								}
							});
						}
					});
		
					// Hide a Column
					$('body').on('click', '.column_hide', function() {
						var num = $('.member_details').length;
						if(num > 1) {
							$(this).parents('.member_details').find('.column_container').fadeOut("slow");
							$(this).prop('class', 'column_show');
							$(this).children('.dashicons-fullscreen-alt').prop('class', 'dashicons dashicons-fullscreen-exit-alt');
							$(this).children('input').prop('value', 'hide');
						}
					});
		
					// Show a Column
					$('body').on('click', '.column_show', function() {
						$(this).parents('.member_details').find('.column_container').fadeIn("slow");
						$(this).prop('class', 'column_hide');
						$(this).children('.dashicons-fullscreen-exit-alt').prop('class', 'dashicons dashicons-fullscreen-alt');
						$(this).children('input').prop('value', 'show');
					});

					// Edit team member, member info and column settings
					$('#rwctm_save').on('click', function() {
						$.rwctmsetmemberoptions();
					});
				},
				complete:function(){
					// Hide image container
					$("#rwctm-loading-image").hide();
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
		});
		// Customizing to add 18 color palettes
		if (typeof $.wp !== 'undefined' && typeof $.wp.wpColorPicker !== 'undefined') {
			$.wp.wpColorPicker.prototype.options = {
				width: 255,
				hide: true,
				border: false,
				palettes: ['#ededed', '#ecf0f1',  '#c8d6e5', '#7f8c8d', '#34495e', '#22313f', '#2ecc71', '#48b56a', '#0abde3', '#1f8dd6', '#2574a9', '#1f3a93', '#5f27cd', '#fad232','#ff9f43', '#ed6789', '#ff6b6b', '#ee5253'],    
			};
		}
	};
	// save member options
	$.rwctmsetmemberoptions = function () {
		// let submitted = false;
		var submitted = $('#submitted').val();
		// Get the form.
		const form = $('#rwctm_edit_form');
		// Get the messages div.
		const formMessages = $('#form-messages');
		// Bind the click event of the submit button
		form.on('submit', function(event) {
			// Prevent the form from submitting normally
			event.preventDefault();
			// Get the form data
			const formData = $(this).serialize();

			// Submit the form via AJAX
			$.ajax({
				type: 'POST',
				url: $(this).attr('action'),
				data: formData,
				success: function(response) {
					// Make sure that the formMessages div has the 'success' class.
					$(formMessages).addClass('success').css('display', 'block');
					// Clear the form and retrieve it again.
					$(form).hide().fadeIn(1000);
					$('html, body').animate({ scrollTop: 0 }, 0);
					$('body').on('click', '.rwctm_close', function() {
						$(formMessages).fadeOut('slow');
					});

					if (submitted === 'no') {
						window.location.reload();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// Display an error message
					alert('An error occurred: ' + textStatus + ' - ' + errorThrown);
				}
			});
		});
	};
	// Edit Activity
	$.rwctmeditactivity = function() {
		var tname;
		$('body').on('click', '.rwctm_activity', function() {
			tname = $(this).attr('data-id');
			$.ajax({
				type: 'POST',
				url: rwctmajax.ajaxurl,
				data: {
					action: 'rwctm_process_activity_option',			// The function that will edit activities
					teamname: tname,
					nonce: rwctmajax.nonce
				},
				beforeSend: function(){
					// Show image container
					$("#rwctm-loading-image").css("display","inline-block");
				},
				success:function(data, textStatus, XMLHttpRequest){
					var linkid = '#rwctm_list';
					$(linkid).html('');
					$("#new_team").hide();
					$('#rwctm-sidebar').hide();
					$('#rwctm-narration').hide();
					$(".team_list .subsubsub").hide();
					$('.regen_shortcode').hide();
					$(linkid).append(data);
					$("#add_new_team h2").text("Edit RWC Team Member Activities");
					$.rwctmSelectOption();
		
					// Edit activities when click on Edit Activities link
					var activityName = $('#activity_edititem');
					$('body').on('click', '#editactivity', function() {
						$('<tr class="activitybody"><td><span>Name:</span><input type="text" name="activity_name[]" placeholder="Enter Activity Name" size="15" required /></td><td><span>https://</span><input type="text" name="activity_link[]" value="" placeholder="Enter Activity URL" size="25" /></td><td><input type="text" name="awesome_icon[]" value="" placeholder="Enter Icon Class" size="15" /><span>Icon</span></td><td><select name="activity_type[]" id="activity_type"><option value="sector" selected="selected">Sector</option><option value="status">Status</option><option value="social">Social</option></select></td><td><span id="remActivity"></span></td></tr>').appendTo(activityName);
						return false;
					});

					// Remove activities
					$('body').on('click', '#remActivity', function() {
						$(this).parents('tr.activitybody').remove();
						return false;
					});

					/* Preventing activity deletion if only one */
					jQuery('body').on('click', '#remActivity', function() {
						var num = jQuery('#activity_edititem tr.activitybody').length;
						// alert(num);
						if (num === 1)
							jQuery('#remActivity').attr('id', 'remDisable');
						jQuery(this).parents('tr.activitybody').remove();
						return false;
					});

					$(function() {
						$('#activity_edititem tbody').sortable({
							helper: function(e, ui) {
								ui.children().each(function() {
									$(this).width($(this).width());
								});
								return ui;
							},
							cancel: 'input, select'
						});
						// Bind touchstart event to input elements
						$('.activitybody input').on('touchstart', function(e) {
							e.preventDefault();
							$(this).focus();
							var val = $(this).val();
							$(this).val('').val(val); // Move cursor to end of input field
						});
					});

					// Add team activities
					$('#rwctm_addactivity').on('click', function() {
						$.rwctmsetmemberoptions();
					});

					// Edit team activities
					$('#rwctm_upactivity').on('click', function() {
						$.rwctmsetmemberoptions();
					});
				},
				complete:function(){
					// Hide image container
					$("#rwctm-loading-image").hide();
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
		});
	};
	// Preview Pricing Table
	$.rwctmviewteam = function () {
		var tmbid;
		var tmname;
		$('body').on('click', '.view_team', function() {
			tmbid = $(this).attr('data-id');
			tmname = $(this).attr('data-team');
			$.ajax({
				type: 'POST',
				url: rwctmajax.ajaxurl,
				data: {
					action: 'rwctm_view_team_member',			// The function that will preview a team
					teamname: tmname,
					teamid: tmbid,
					nonce: rwctmajax.nonce
				},
				beforeSend: function(){
					// Show image container
					$("#rwctm-loading-image").css("display","inline-block");
				},
				success:function(data, textStatus, XMLHttpRequest){
					var linkid = '#rwctm_list';
					var replace_name = tmname.replace("_", " ");
					var preview_team_name = replace_name.replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function(m){ return m.toUpperCase(); });
					$(linkid).html('');
					$("#new_team").hide();
					$('#rwctm-sidebar').hide();
					$('#rwctm-narration').hide();
					$(".team_list .subsubsub").hide();
					$('.regen_shortcode').hide();
					$(linkid).append(data);
					$("#add_new_team h2.main-header").text("Preview of " + preview_team_name);
					$(".team_list").css("width","100%");
					$(".postbox-container").css("width","100%");

					// Edit pricing column, column info and column settings
					$('.process_team').on('click', function() {
						$.rwctmsetupmember();
					});
				},
				complete:function(){
					// Hide image container
					$("#rwctm-loading-image").hide();
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			});
			//e.preventDefault();
		});
	};
	/**
	 * 
	 * rwctmMediaUploader v1.0 2019-10-04
	 * Copyright (c) 2019 rwctm
	 * 
	 */
	$.rwctmMediaUploader = function( options ) {
		var settings = $.extend({
			target : '.rwctm-uploader', // The class wrapping the textbox
			uploaderTitle : 'Select or upload image', // The title of the media upload popup
			uploaderButton : 'Set image', // the text of the button in the media upload popup
			multiple : false, // Allow the user to select multiple images
			buttonText : 'Upload image', // The text of the upload button
			buttonClass : '.rwctm-upload', // the class of the upload button
			previewSize : '', // The preview image size
			modal : false, // is the upload button within a bootstrap modal ?
			buttonStyle : { // style the button
				color : '#fff',               
			},
		}, options );

		$( settings.target ).append( '<a href="#" class="button-primary ' + settings.buttonClass.replace('.','') + '">' + settings.buttonText + '</a>' );
		$( settings.target ).append('<div id="show_preview" style="display: none;"><label class="input-image">Preview</label><img class="preview_image" src="#" style="display: none;"><span id="remove_image"></span></div>');
		$( settings.buttonClass ).css( settings.buttonStyle );

		$('body').on('click', settings.buttonClass, function(e) {
			e.preventDefault();
			var selector = $(this).parent( settings.target );
			var custom_uploader = wp.media({
				title: settings.uploaderTitle,
				button: {
					text: settings.uploaderButton
				},
				multiple: settings.multiple
			})

			.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				selector.find( '#show_preview' ).slideDown("slow");
				selector.parents('.element-input').find('#show_upload_preview').hide();
				selector.find( 'img' ).attr( 'src', attachment.url).show();
				selector.find( 'input' ).val(attachment.url);
				if( settings.modal ) {
					$('.modal').css( 'overflowY', 'auto');
				}
				// Remove image
				$('body').on('click', '#remove_image', function() {
					var targetfield = $(this).parents('.element-input').find('#image_path');
					$(targetfield).val('');
					$(this).parents('#show_preview').slideUp('slow');
				});
			})
			.open();
		}); 
	};
	// Iterate over each select element
	$.rwctmSelectOption = function() {
		$('select').each(function () {

			// Cache the number of options
			var $this = $(this),
				numberOfOptions = $(this).children('option').length;

			// Hides the select element
			$this.addClass('s-hidden');

			// Wrap the select element in a div
			$this.wrap('<div class="select'+(($this.attr("data-class")) ? " "+$this.attr("data-class") : "")+'"></div>');

			// Insert a styled div to sit over the top of the hidden select element
			$this.after('<div class="styledSelect"></div>');

			// Cache the styled div
			var $styledSelect = $this.next('div.styledSelect');

			var $selected = $this.children('option:selected');

			var $contentSelected = (($selected.attr("data-photo")) ? '<img src="'+$selected.attr("data-photo")+'" />' : "") + (($selected.attr("data-icon")) ? '<span class="ir ico '+$selected.attr("data-icon")+'"></span>' : "") + $selected.text();

			// Show the first select option in the styled div
			$styledSelect.html($contentSelected);
			// Add selected option value as a class to the styled div
			$styledSelect.addClass($selected.val());

			// Insert an unordered list after the styled div and also cache the list
			var $list = $('<ul />', {
				'class': 'options',
			}).insertAfter($styledSelect);

			// Insert a list item into the unordered list for each select option
			for (var i = 0; i < numberOfOptions; i++) {
				var content = (($this.children('option').eq(i).attr("data-photo")) ? '<img src="'+$this.children('option').eq(i).attr("data-photo")+'" />' : "") + (($this.children('option').eq(i).attr("data-icon")) ? '<span class="ir ico '+$this.children('option').eq(i).attr("data-icon")+'"></span>' : "") + $this.children('option').eq(i).text();
				var $option = $this.children('option').eq(i);
	
				$('<li />', {
					html: content,
					rel: $this.children('option').eq(i).val(),
					"class": (($selected.val() == $option.val()) ? 'active' : '') +
								($option.attr('disabled') ? ' disabled' : ''),
					"data-photo": $option.attr("data-photo"),
					"data-icon": $option.attr("data-icon")
				}).appendTo($list);
			}

			// Cache the list items
			var $listItems = $list.children('li');

			if($this.attr("data-error")){
				$this.parent().append('<div class="select__error">'+ $this.attr("data-error") +'</div><div class="select__ico select__ico--error"></div></div><div class="select__ico select__ico--ok"></div>');
			}

			// Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
			$styledSelect.click(function(e) {
				e.stopPropagation();
				if (!$(this).hasClass("active")) {
					$('div.styledSelect.active').each(function() {
						$(this).removeClass('active').next('ul.options').hide();
					});
				}
				$(this).toggleClass('active').next('ul.options').toggle();
			});

			// Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
			// Updates the select element to have the value of the equivalent option
			$listItems.click(function(e) {
				e.stopPropagation();
				var $selected = $(this);
				if ($selected.hasClass('disabled')){return;}
				var $contentSelected = (($selected.attr("data-photo")) ? '<img src="'+$selected.attr("data-photo")+'" />' : "") + (($selected.attr("data-icon")) ? '<span class="ir ico '+$selected.attr("data-icon")+'"></span>' : "") + $selected.text();
				$styledSelect.html($contentSelected).removeClass('active');
	
				if ($this.val() != $(this).attr('rel')) {
					$this.val($(this).attr('rel')).change();
				}
	
				$list.hide();
			});

			// Hides the unordered list when clicking outside of it
			$(document).click(function () {
				$styledSelect.removeClass('active');
				$list.hide();
			});
		});
	};
})(jQuery);