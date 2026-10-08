/* Posterity theme dashboard + admin notices */
(function ($) {
	'use strict';
	if (typeof posterityTI === 'undefined') { return; }

	// Tabs on the theme dashboard page.
	$(document).on('click', '.posterity-ti-wrap .tablinks', function () {
		var $btn = $(this), $wrap = $btn.closest('.posterity-ti-wrap');
		$wrap.find('.tabcontent').hide().removeClass('open');
		$wrap.find('.tablinks').removeClass('active');
		$wrap.find('#' + $btn.data('tab')).show().addClass('open');
		$btn.addClass('active');
	});

	function dismiss(which) {
		$.post(posterityTI.ajaxurl, { action: 'posterity_ti_dismiss_notice', notice: which, nonce: posterityTI.nonce });
	}

	// Welcome notice: WordPress core adds the "X" button for .is-dismissible.
	$(document).on('click', '.posterity-ti-welcome .notice-dismiss', function () {
		dismiss('welcome');
	});

	// Bundle notice.
	$(document).on('click', '.posterity-ti-bundle-dismiss', function () {
		$(this).closest('.posterity-ti-bundle-notice').fadeOut(200);
		dismiss('bundle');
	});

	// GET STARTED: install + activate SKT Templates, then open the theme dashboard.
	$(document).on('click', '#posterity-ti-get-started', function (e) {
		e.preventDefault();
		var $btn = $(this);
		if ($btn.hasClass('is-busy')) { return; }
		$btn.addClass('is-busy').text(posterityTI.installing).css('pointer-events', 'none');

		$.post(posterityTI.ajaxurl, { action: 'posterity_ti_install_companion', nonce: posterityTI.nonce })
			.done(function (res) {
				if (res && res.success) {
					$btn.text(posterityTI.activated);
					window.location.href = res.data.redirect;
				} else {
					window.alert((res && res.data && res.data.message) || posterityTI.failed);
					$btn.removeClass('is-busy').text(posterityTI.tryAgain).css('pointer-events', 'auto');
				}
			})
			.fail(function () {
				window.alert(posterityTI.failed);
				$btn.removeClass('is-busy').text(posterityTI.tryAgain).css('pointer-events', 'auto');
			});
	});
})(jQuery);
