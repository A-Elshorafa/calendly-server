<!Doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Calendly Demo </title>
    <style>
      label {
        color: gray;
        font-size: 14px;
      }
    </style>
  </head>
  <body>
    <h2>Hi {{$data['host']['name']}},</h2>
    <h2>A new event has been scheduled.</h2>
    <h3>Event Type:</h3>
    <label>One-off meeting</label>
    <br/>
    <h3>Invitee:</h3>
    <label>{{ $data['attendee']['name'] }}</label>
    <br/>
    <h3>Invitee Email:</h3>
    <a>{{ $data['attendee']['email'] }}</a>
    <br/>
    <h3>Event Date:</h3>
    <label>{{ $data['event']['subscribed_on'] }}</label>
    <br/>
    <h3>Calendly Demo Link:</h3>
    <label>{{ $data['event']['calendly_link'] }}</label>
    <br/>
    <h3>{{$data['event']['third_party_name']}} Link:</h3>
    <label>{{ $data['event']['third_party_link'] }}</label>
    <br/>
    <h3>{{$data['event']['third_party_name']}} Password:</h3>
    <label>{{ $data['event']['password'] }}</label>
    <br/>
    <br/>
    <h3>Powered By Calendly Demo.</h3>
  </body>
</html>
