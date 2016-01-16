/**
 * Created by johan on 02-Dec-15.
 */
function showAjaxErrorModal(message)
{
    var modal = $('');
    modal.show();
    //make error modal unhidden
    //add click listener to see if person clicks outside the modal
    $(modal, '.modal-button').click(hideModal(modal));
    $(modal, '.modal-message').html(message);
    //set content (right tag) to message
}

//Hides the given modal. The modal is the entire modal div
function hideModal(modal) {
    $(modal).hide();
    $(modal, '.modal-message').html('');
    //empty content
}