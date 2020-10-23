<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Payment Approved</title>
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
        <td>
          <div
            width="700"
            style="
              background-color: #0275d8;
              color: #fefefe;
              align-self: center;

              font-size: 1rem;

              padding: 1rem 2rem;
              border-radius: 0.3rem;
              border: none;

              text-align: center;
            "
          >
            <p>
              <span style="font-weight: 600">Hallo {{$name}},</span><br />
              Thanks for your payment!
            </p>
          </div>
        </td>
      </tr>
      <tr>
        <td bgcolor="#fff" style="font-size: 1rem; padding: 1rem 1rem 0 1rem">
          <p
            style="
              font-weight: 600;
              color: #aaa;
              border-bottom: 1px solid #ccc;
              margin: 2rem 0 0 0;
            "
          >
            We attach your receipt to this email
          </p>
        </td>
      </tr>
      <tr>
        <td bgcolor="#fff" style="font-size: 1rem; padding: 0 1rem 1rem 1rem">
          <div style="margin: 1rem 0 2rem 0">
            <p style="margin: 0">
              <span style="font-weight: 600">Invoice Number:</span
              ><br />{{$number}}
            </p>
          </div>

          <h4 style="color: #aaa; margin: 0 0 0 0">HERE'S WHAT YOU ORDERED:</h4>

          <div
            style="
              display: block;
              float: left;
              width: 302px;
              background-color: #eee;
              padding: 0 1rem 0 1rem;
              margin: 1rem 0 0 0;
            "
          >
            <p>Description</p>
          </div>
          <div
            style="
              display: block;
              float: right;
              background-color: #eee;
              width: 302px;
              padding: 0 1rem 0 1rem;
              margin: 1rem 0 0 0;
            "
          >
            <p style="text-align: right">Price</p>
          </div>

          <div
            style="
              display: block;
              float: left;
              width: 302px;
              padding: 0 1rem 0 1rem;
            "
          >
            <p>{{$packageName}}</p>
            <p
              style="
                text-align: right;
                font-weight: 800;
                font-size: 1.2rem;
                margin: 2rem 0 2rem 0;
              "
            >
              TOTAL
            </p>
          </div>
          <div
            style="
              display: block;
              float: right;
              width: 302px;
              padding: 0 1rem 0 1rem;
            "
          >
            <p style="text-align: right">IDR {{$price}}</p>
            <p
              style="
                text-align: right;
                font-weight: 800;
                font-size: 1.2rem;
                margin: 2rem 0 2rem 0;
              "
            >
              IDR {{$price}}
            </p>
          </div>
        </td>
      </tr>
      <tr>
        <td bgcolor="#fff" style="font-size: 1rem; padding: 0 1rem 1rem 1rem">
          <p>
            Regards, <br /><span style="font-weight: 600">Meet-Up Team</span>
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
          <p>
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
