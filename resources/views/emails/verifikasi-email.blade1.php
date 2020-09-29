<!DOCTYPE html>
<html>
  <head>
    <!--begin::Fonts -->
    <!-- <style>
      @font-face {
        font-family: "poppins";
        src: url("https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap");
      }
      @font-face {
        font-family: "roboto";
        src: url("https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap");
      }
    </style> -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <!-- <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap"
      rel="stylesheet"
    />
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    -->

    <!--end::Fonts -->
  </head>
  <body style="background-color: #ccc">
    <!-- container -->
    <div
      style="
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        height: auto;
        align-items: center;
        justify-content: center;

        font-family: 'Poppins';
        padding: 7rem;
      "
    >
      <div
        style="
          border: none;
          background-color: #fff;
          width: 50vw;
          padding: 2rem;
          display: flex;
          flex-direction: column;
          font-size: 1rem;
        "
      >
        <img
          src="http://up.mitratekno.co.id/assets/extends/media/img/logo-meetup/meetup-blue.png"
          style="width: 10vw; align-self: center; flex: 1; margin-bottom: 3rem"
        />
        <p style="font-weight: 600; color: #777">Hello {{$name}},</p>
        <p style="color: #777">
          Thanks for joining Meet-Up to gain access for your account, please
          validate your email. Please click the button bellow to validate your
          email :
        </p>
        <button
          type="button"
          style="
            background-color: #0275d8;
            color: #fefefe;
            align-self: center;
            flex: 1;
            font-size: 1rem;
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 0.3rem;
            border: none;
            margin: 2rem 0 2rem;
          "
        >
          Activate Account
        </button>
        <p style="color: #aaa">
          if the button doesn't work, visit the link below :
        </p>
        <a style="font-weight: 600; color: #0275d8">{{$url}}</a>

        <p>Thanks, <br /><span style="font-weight: 600">Meet-Up Team</span></p>
      </div>
      <div
        style="
          background-color: #fcc018;
          width: 50vw;
          padding: 1rem 2rem;
          display: flex;
          flex-direction: column;
          font-size: 1rem;
          text-align: center;
        "
      >
        <p style="flex: 1">
          <span style="font-weight: 600">Meet UP</span><br />
          Jl. Barata Jaya III no.16, Kec.Gubeng, Kota SBY, Jawa Timur 60284<br />
          +6231-999099<br />
          2020 Meet UP Team - All Right Reserved
        </p>
      </div>
    </div>
  </body>
</html>
