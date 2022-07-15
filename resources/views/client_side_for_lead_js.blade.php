<script>
function postAjax(data, success) {
    // Change the domain name in production.
    var url = 'http://127.0.0.1:8000/api/savelead';
    var params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
        ).join('&');

    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('auth_token', 'fP7VwPDn6JA7RT2B/i7bFxRUnnK8Xro5BwPH/CwYZqnnVhbWfA/3GMSVyB40ATy4XRdqSnQ0LVM/HUwckS1KgmStNlfh+n6R+9vQIOX1u1w=');
    xhr.send(params);
    return xhr;
}

function submitRequest(){
    // example request with data object
    //$data = ['cb_account'=>'GODFREQ', 'affiliate_id'=>'ASTRAL43'];
    postAjax({ cb_account: 'GODFREQ', affiliate_id: 'ALVINW88', email: 'temp2@gmail.com' }, function(data){ console.log(data); });
}
</script>
