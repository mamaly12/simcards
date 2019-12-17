users = document.getElementById('users');
if(users)
{
    users.addEventListener('click', event => {
        if(event.target.className === 'btn btn-danger delete-user')
    {
        if(confirm('Are you sure?'))
        {
            const id = event.target.getAttribute('data-id');
            const path = event.target.getAttribute('data-path');
            $.ajax({
                url: path,
                type: "DELETE",
                data: { "id": id,},
                dataType: "json",
                success : function(data) {
                    if ( data.result ) {
                        location.reload();
                    }
                }
            });
        }
    }
    });
}