$(".company-id-select").select2({
    placeholder: "<?php _trans('company'); ?>",
    ajax: {
        url: "<?php echo site_url('companies/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_clients: $('input#input_permissive_search_clients').val(),
                page: params.page,
                _ip_csrf: Cookies.get('ip_csrf_cookie')
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    minimumInputLength: 1
});
