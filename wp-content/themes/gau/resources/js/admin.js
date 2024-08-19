import { nanoid } from 'nanoid';
import Cookies from 'js-cookie';

Object.assign(window, { Cookies });

jQuery(function ($) {
    // Pace
    $(document).ajaxStart(() => {
        Pace.restart();
    });

    // user
    const create_user = $('#createuser');
    create_user.find('#send_user_notification').removeAttr('checked').attr('disabled', true);
});
