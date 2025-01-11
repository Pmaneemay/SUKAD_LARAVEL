<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/M_team_management.css') }}">
    <title>Team Management Page</title>
</head>
<body>

    <x-taskbar />
    <div id="dashboard">
        <div id="team_profile">
            <div id="desasiswa_banner"></div>
            <ul>
                <li>
                    <div id="team_logo"; style="background-color: white;"></div>
                </li>
                <li>
                    <div id="team_stat">
                        
                    </div>
                </li>
            </ul> 
        </div>

        <x-M_team_navigation />

        <div id="table_container"></div>
    </div>

</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let role = session('role');
    let desasiswa_id = session('profile').desasiswa_id;

    get_team_Manager(desasiswa_id);

   
});

function get_team_Manager(desasiswa_id){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getManagers') }}",
        data : {
            'desasiswa_id' : desasiswa_id
        },
        type : 'GET',
        dataType : 'json',
        success : function(res){
            console.log(res.data);

        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
};


</script>