/**
 * Team JavaScript
 *
 * This JavaScript file contains scripts responsible for the frontend functionality
 * of the "RWC Team Members" plugin. These scripts handle features such as
 * the filter navigation system, enhancing the team layout, and managing the left popup.
 *
 * @package RWC Team Members - v0.5 - 25 July, 2024
 */
(function($) {
	'use strict';

	var $teams = $('.rwctm-container');

	$teams.each(function() {
		var $team = $(this);
        var $filters = $team.find('.rwctm-team-filter-wrap [data-filter]');
        var $boxes = $team.find('.rwctm-row [data-category]');

		// Hide empty departments initially
		var $departments = $team.find('.rwctm-team-filter-wrap [data-filter]');
		// var $departments = $('.rwctm-team-filter-wrap [data-filter]');
		$departments.each(function() {
			var $department = $(this).attr('data-filter');
			var $members = $team.find('.rwctm-row [data-category*="' + $department + '"]');
			// var $members = $('.rwctm-row [data-category*="' + $department + '"]');
			if ($department !== 'all' && $members.length === 0) {
				$(this).hide();
			}
		});

		$filters.on('click', function(e) {
			e.preventDefault();
			var $this = $(this);

			$filters.removeClass('rwctm-active-filter');
			$this.addClass('rwctm-active-filter');

			var $filterColor = $this.attr('data-filter');

			if ($filterColor === 'all') {
				$boxes.removeClass('animate__animated ').fadeOut().promise().done(function() {
					$boxes.addClass('folding-parent animate__animated ').fadeIn();
					$(".folding-parent").css('margin', '');
					$(".folding-parent:last-child").css('margin-right', '');
				});
			} else {
				$boxes.removeClass('animate__animated ').fadeOut().promise().done(function() {
					$boxes.removeClass('folding-parent');
					$boxes.filter('[data-category *= "' + $filterColor + '"]').addClass('folding-parent animate__animated ').fadeIn();
					var $margin = $(".folding-parent").css('margin-right');
					$(".folding-parent").css('margin', '0 ' + $margin + ' ' + $margin + ' 0');
					$(".folding-parent:last-child").css('margin-right', '0');
				});
			}
		});
    });

	// for layout 9
	var $lout3 = $('.rwctm-member.lout3');
	if ($lout3.length) {
		$lout3.each(function() {
			var $memberInfo = $(this).find('.rwctm-member-info');
			var $statusAndBio = $memberInfo.find('.rwctm-member-status, .rwctm-member-bio');
			if (!$memberInfo.closest('.rwctm-info-wrapper').length) {
				// Create a new div with class 'rwctm-info-wrapper'
				var $wrapperDiv = $('<div>').addClass('rwctm-info-wrapper');
				// Append the new div to the 'rwctm-member-info' div
				$wrapperDiv.append($statusAndBio);
				$memberInfo.append($wrapperDiv);
			}
		});
	}

	// for layout 5
	var $lout5 = $('.rwctm-member.lout5');
	if ($lout5.length) {
		$lout5.each(function() {
			var $memberBio = $(this).find('.rwctm-member-bio');
			var $BioDetails = $memberBio.find('.rwctm-short-bio, .social-thumb');
			if (!$memberBio.closest('.rwctm-bio-wrapper').length) {
				// Create a new div with class 'rwctm-bio-wrapper'
				var $wrapperDiv = $('<div>').addClass('rwctm-bio-wrapper');
				// Append the new div to the 'rwctm-member-info' div
				$wrapperDiv.append($BioDetails);
				$memberBio.append($wrapperDiv);
			}
		});
	}

	/* Pop-up Left */
	var $popup = $('.rwctm-modal-corner');
	// alert($popup.length);
	if ($popup.length) {
		$(document).ready(function() {
			// Iterate through each team container
			$('.rwctm-popup-corner').each(function () {
				var $teamContainer = $(this);
				// var team = $teamContainer.attr('id'); // Get the team ID or any other identifier you use

				if ($('.rwctm-member-corner').length > 0) {
					$('.rwctm-member-corner').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						// $(this).find('.rwc-info-group').addClass('isActive');
						$('.rwc-info-group').removeClass('isActive'); // Close any other open pop-ups
						$(this).find('.rwc-info-group').addClass('isActive');
					});
				}

				if ($teamContainer.find('.close-side-widget').length > 0) {
					$teamContainer.find('.close-side-widget').on('click', function (e) {
						e.preventDefault();
						$(this).closest('.rwc-info-group').removeClass('isActive');
					});
				}

				$('body').on('click', function(e) {
					$('.rwc-info-group').removeClass('isActive');
				});

				$teamContainer.find('.rwctm-modal-content').on('click', function (e) {
					e.stopPropagation();
				});
			});
		});
	}
})(jQuery);