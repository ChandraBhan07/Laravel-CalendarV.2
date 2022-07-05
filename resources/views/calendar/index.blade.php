<html lang='en'>

<head>
    <meta charset='utf-8' />
    <link href='./fullcalendar/main.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200&display=swap" rel="stylesheet">

    <script src='./fullcalendar/main.js'></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            let calendarEl = document.getElementById('calendar');
            let data = @json($dataArr);
            console.log(data);
            let calendarParams = {
                events: data,



                customButtons: {
                    createEventBtn: {
                        text: 'Create Event',
                        click: function() {
                            location.href = '/create-events';
                        }
                    },

                    logoutButton: {
                        text: 'Logout',
                        click: function() {
                            axios.post('/logout', {

                                })
                                .then(function(response) {
                                    if(response == 'ok'){
                                        console.log('yes');
                                        location.href = '/login';
                                    }
                                })
                                .catch(function(error) {
                                    console.log(error);
                                });
                        }
                    },
                },

                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'createEventBtn, logoutButton'
                }

            };
            let calendar = new FullCalendar.Calendar(calendarEl, calendarParams);

            calendar.setOption('height', '95%');

            calendar.render();
        });
    </script>
</head>

<body style="font-family: 'Nunito', sans-serif;">
    <div class="" style="margin: 25px ">

        <div id='calendar'></div>
    </div>
</body>

</html>
