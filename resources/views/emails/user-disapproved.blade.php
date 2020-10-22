<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Payment Declined</title>
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
          <p style="font-weight: 600; color: #777">Hello {{$name}},</p>
          <p style="color: #777; margin: 0 0 3rem 0">
            Glad to meet you<br />
            Unfortunately, Meet UP team has chosen not to move forward with your
            account.<br />
            <br />
            <span style="font-weight: 600; color: #666">Reason</span><br />
            {{$reason}}
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
