<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Send Invitation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--begin::Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <!--end::Fonts -->
  </head>
  <body bgcolor="#ccc" style="margin: 0; padding: 0; font-family: 'Poppins'">
    <table
      align="center"
      border="0"
      cellpadding="0"
      cellspacing="0"
      width="700"
      style="margin: 2rem auto 2rem auto"
    >
      <tr>
        <td align="center" bgcolor="#fff" style="padding: 40px 0 30px 0">
          <img
            src="http://up.mitratekno.co.id/assets/extends/media/img/logo-meetup/meetup-blue.png"
            alt="Meet UP"
            width="150"
            height="50"
            style="display: block"
          />
        </td>
      </tr>
      <tr>
        <td
          bgcolor="#fff"
          style="font-size: 1rem; padding: 1rem 1rem 1rem 1rem"
        >
          <p style="font-weight: 600; color: #777">Hello {{$email}}</p>
          <p style="color: #777">
            {{$name}} invited you to join The Meet Up, to complete the action click the button bellow
          </p>
          <a
            href="{{$url}}"
            style="
              background-color: #0275d8;
              color: #fefefe;
              display: block;
              font-size: 1rem;
              font-weight: 600;
              padding: 1rem 2rem;
              border-radius: 0.3rem;
              border: none;
              margin: 2rem auto 2rem auto;
              width: 150px;
              text-align: center;
              text-decoration: none;
            "
          >
            Register Now
          </a>
          <p style="color: #aaa; margin: 0 0 0 0">
            if the button doesn't work, visit the link below :
          </p>
          <a href="{{$url}}" style="font-weight: 600; color: #0275d8; margin: 0 0 0 0"
            >{{$url}}</a
          >

          <p style="margin: 3rem 0 0 0">
            Thanks, <br /><span style="font-weight: 600">Meet-Up Team</span>
          </p>
        </td>
      </tr>
      <tr>
        <td
          bgcolor="#fcc018"
          style="
            font-size: 1rem;
            text-align: center;
            padding: 1rem 1rem 1rem 1rem;
          "
        >
          <p style="flex: 1">
            <span style="font-weight: 600">Meet UP</span><br />
            Jl. Barata Jaya III no.16, Kec.Gubeng, Kota SBY, Jawa Timur 60284<br />
            +6231-999099<br />
            2020 Meet UP Team - All Right Reserved
          </p>
        </td>
      </tr>
    </table>
  </body>
</html>
