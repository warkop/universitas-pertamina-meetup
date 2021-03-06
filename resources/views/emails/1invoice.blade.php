<!DOCTYPE html>
<html lang="en">
  <head>
    <!--begin::Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <!--end::Fonts -->
  </head>
  <body style="background-color: #ccc">
    <!-- container -->
    <div
      style="
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: 'Poppins';
        padding: 7rem;
        height: auto;
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
          style="width: 10vw; align-self: center; flex: 1"
        />
        <div
          style="
            background-color: #0275d8;
            color: #fefefe;
            align-self: center;
            flex: 1;
            font-size: 1rem;

            padding: 1rem 2rem;
            border-radius: 0.3rem;
            border: none;
            margin: 2rem 0 2rem;
            text-align: center;
            width: 50vw;
          "
        >
          <p>
            <h2>MEET UP BILLING INFORMATION</h2>
          </p>

        </div>
        <p style="font-size:1rem;font-weight: 600; color: #777;margin-bottom: 0;;">Hi {{$name}},</p>
        <p style="font-size:1rem;color: #777">
          Your package almost over, you can <span style="color:#0275d8;font-weight: 600;">renew</span> package or <span style="color:#0275d8;font-weight: 600;">upgrade</span> package<br>
          for still using all service
        </p>
        <p style="font-weight: 600; color: #aaa; border-bottom: 1px solid #ccc">
          YOUR ORDER INFORMATION
        </p>
        <div
            style="
              margin-bottom: 1rem;
              display: flex;
              flex-direction: row;
            "
          >
            <p style="flex: 1;margin:0"><span style="font-weight: 600;">Order Date:</span><br>{{$orderDate}}</p>
            <p style="flex: 1;margin:0"><span style="font-weight: 600;">Expiration Limit:</span><br>{{$expirationDate}}</p>

          </div>
          <div
            style="
              margin-bottom: 1rem;
              display: flex;
              flex-direction: row;
            "
          >
            <p style="flex: 1;margin:0"><span style="font-weight: 600;">Bill To:</span><br>{{$email}}</p>
            <p style="flex: 1;margin:0"><span style="font-weight: 600;">Package:</span><br>{{$packageName}}</p>

          </div>

        <h4 style="color: #aaa">HERE'S WHAT YOU ORDERED:</h4>
        <div>
          <div
            style="
              background-color: #ddd;
              padding: 1rem;
              display: flex;
              flex-direction: row;
            "
          >
            <p style="flex: 1">Description</p>
            <p style="text-align: right; flex: 1">Price</p>
          </div>
        </div>
        <div>
          <div style="padding: 1rem; display: flex; flex-direction: row">
            <p style="flex: 1">{{$packageName}}</p>
            <p style="text-align: right; flex: 1">IDR {{$price}}</p>
          </div>
          <div
            style="
              padding: 0 1rem 1rem 1rem;
              display: flex;
              flex-direction: row;
            "
          >
            <p style="flex: 1"></p>
            <p style="flex: 1"></p>
            <p
              style="
                flex: 1;
                font-weight: 800;
                font-size: 1.2rem;
                margin-top: 0;
              "
            >
              TOTAL
            </p>
            <p
              style="
                margin-top: 0;
                text-align: right;
                flex: 1;
                font-weight: 800;
                font-size: 1.2rem;
              "
            >
              IDR {{$price}}
            </p>
          </div>
        </div>
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
        Pay Now
      </button>
        <p>Regards, <br /><span style="font-weight: 600">Meet-Up Team</span></p>
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
