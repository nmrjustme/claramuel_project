<!DOCTYPE html>
<html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
</head>
<body>
    <h1>Pusher Test</h1>
    <script>
        const pusher = new Pusher('{{config("broadcasting.connections.pusher.key")}}', {
            cluster: '{{config("broadcasting.connections.pusher.options.cluster")}}'
        });
        
        const channel = pusher.subscribe('booking-log-channel');
        
        channel.bind('new-booking-log', function(data) {
            console.log('TEST EVENT RECEIVED:', data);
            alert('Event received! Check console');
        });
        
        console.log('Test script loaded, waiting for events...');
    </script>
</body>
</html>