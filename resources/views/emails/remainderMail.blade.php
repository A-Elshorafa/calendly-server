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
    <h3>Event Name:</h3>
    <label>{{ $data['event']['name'] }}</label>
    <br/>
    @if($data['toHost'])
    <h3>attendee can join meeting using this link:</h3>
    @else
    <h3>you can join by this link:</h3>
    @endif
    <label>{{ $data['event']['third_party_link'] }}</label>
    <br/>
    <h3>{{$data['event']['third_party_name']}} Password:</h3>
    <label>{{ $data['event']['password'] }}</label>
    <br/>
    <h3>Notes:</h3>
    <label>{{ $data['attendee']['notes'] }}</label>
    <br/>
    <br/>
    <h3>Powered By Calendly Demo.</h3>
  </body>
</html>
