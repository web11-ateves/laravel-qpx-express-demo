$(document).ready(function() {

    $('body').delegate('[data-form-link]', 'click', function(e) {

        var $self = $(this);

        function submitForm()
        {
            var method = $self.data('method');
            var action = $self.data('action');
            var token = $self.data('token');
            var form = $('<form method="POST" action="' + action + '"><input type="hidden" name="_method" value="' + method + '"><input type="hidden" name="_token" value="' + token +'"></form>');
            $(document.body).append(form);
            form.submit();
            return true;
        }

        var confirmTitle = $self.data('confirm-title');
        var confirmText = $self.data('confirm-text');

        if (typeof confirmTitle !== typeof undefined && confirmTitle !== false) {

            swal({
                title: confirmTitle,
                text: confirmText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                closeOnConfirm: false
            }, function() {

                submitForm();

            });

        } else {
            submitForm();
        }

        e.preventDefault();
    });

});