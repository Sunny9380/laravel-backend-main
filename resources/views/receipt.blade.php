<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            color: rgb(39, 39, 39);
        }

        body {
            padding: 15px;
            border-radius: 15px;
            font-family: "Roboto", sans-serif;
            background-color: white;
            width: 900px;
            margin: 10px auto;
        }

        .margin-3 {
            margin: 3px;
        }

        .price {
            text-align: center;
            padding: 5px;
            font-weight: bold;
        }

        .logo {
            height: 70px;
            width: auto;
            margin: 0 auto;
            display: block;
        }

        .justify-between {
            display: flex;
            justify-content: space-between;
        }

        .flex-col {
            display: flex;
            flex-direction: column;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-normal {
            font-weight: normal;
        }

    </style>
</head>
<body>
<header class="justify-between">
    <div id="idInfo" class="flex-col font-bold">
        <div>
            <h2>Hotel Bill</h2>
            <p style="font-size: medium; margin-top: 5px">
                Order ID : {{$booking->order_id}}
            </p>
        </div>
        <div style="margin-top: 20px">
            <p>
                Booking ID :
                <span class="font-normal">{{$booking->booking_id}}</span>
            </p>
            <p>
                Invoice Date :
                <span class="font-normal">{{$booking->formatted_created_at_date}}</span>
            </p>
        </div>
    </div>

    <div class="flex-col">
        <div class="logo">
            <img
                src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/logo/logo.png')))}}"
                class="logo" alt="StayMyTrip"
                height="100%"
            />
        </div>
        <p class="font-bold">Staymytrip.com</p>
    </div>
</header>

<section id="billingInfo">
    <div class="contactInfo justify-between">
        <div style="display: flex; margin: 20px 0; flex-direction: column">
            <!-- Billed To -->
            <h5 style="margin: 3px; margin-top: 10px; font-size: medium;">
                Billed to</h5>
            <div style="display: flex; flex-direction: column">
                <p class="margin-3">{{$booking->name ?? '---'}}</p>
                <p class="margin-3">{{$booking->email ?? '---'}}</p>
                <p class="margin-3">{{$booking->primary_phone_number ?? ''}}
                    , {{$booking->primary_phone_number ?? ''}}</p>
            </div>

            <h5 style="margin: 3px; margin-top: 10px; font-size: medium; color: gray">
                GST Details:
            </h5>
            <div style="display: flex; flex-direction: column">
                <p class="margin-3">Name{{$booking->vendor->name}}</p>
                <p class="margin-3">Address {{$booking->vendor->address}}</p>
                <p class="margin-3">GST No. {{$booking->vendor->gst_number}}</p>
            </div>
        </div>
        <!-- Billed From -->
        <div style="display: flex; max-width: 400px; margin: 10px 0; flex-direction: column">
            <h5 style="margin: 5px 3px; font-size: medium">Billed From</h5>
            <div style="display: flex; flex-direction: column">
                <p class="margin-3">{{$booking->hotel->name}}</p>
            </div>

            <div style="display: flex; flex-direction: column">
                <p class="margin-3" style=" display: flex; flex-direction: row;">
                    <svg style="margin-right: 5px; min-height: 20px" xmlns="http://www.w3.org/2000/svg" width="16"
                         height="16"
                         fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                              d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/>
                        <path fill-rule="evenodd"
                              d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                    </svg>
                    {{$booking->hotel->address}}
                </p>
                <p class="margin-3" style=" display: flex; flex-direction: row; flex-wrap: wrap;">
                    <svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                        <path
                            d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                    </svg>
                    {{$booking->hotel->primary_number}}, {{$booking->hotel->primary_number}}
                </p>
                <p class="margin-3" style=" display: flex; flex-direction: row; flex-wrap: wrap;">
                    <svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                    </svg>
                    {{$booking->hotel->primary_email}}, {{$booking->hotel->secondary_email}}
                </p>
                <p class="margin-3" style=" display: flex; flex-direction: row; flex-wrap: wrap;">
                    <svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                        <path
                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472M3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
                    </svg>
                    shepperdhotel.com
                </p>
            </div>
        </div>
    </div>

    <!-- Room Info -->
    <div style="margin: 20px 0px;">
        <h5 style="margin: 5px 0px; font-size: medium">Room Details</h5>
        <div class="roomInfo justify-between">
            <div style="display: flex; margin: 3px; margin-top: 5px; flex-direction: column">
                @foreach($booking->roomDetails as $room_detail)
                    <p>{{$room_detail['roomsCount']}} {{$room_detail['room_name']}} room
                        + {{$room_detail['guestsCount']}} Guests
                    </p>
                @endforeach
            </div>

            <div style="display: flex; flex-direction: column">
                <div>
                    @if($booking->booking_type == 1)
                        <p><span style="font-weight: bold">Booking Type:</span>
                            Overnight
                        </p>
                        <p>
                            <span style="font-weight: bold">Check In:</span>
                            {{$booking->formatted_check_in}}
                        </p>
                        <p>
                            <span style="font-weight: bold">Check Out:</span>
                            {{$booking->formatted_check_out}}
                        </p>
                        <p>
                            <span style="font-weight: bold">Hotel Check in-out time</span>
                            {{$booking ->formatted_hotel_time_in}} - {{$booking->formatted_hotel_time_out}}
                        </p>
                    @elseif($booking->booking_type == 2)
                        <p><span style="font-weight: bold">Booking Type:</span>
                            Hourly
                        </p>
                        <p>
                            <span style="font-weight: bold">Check In:</span>
                            {{$booking->formatted_check_in}}
                        </p>
                        <p>
                            <span style="font-weight: bold">Check In Time:</span>
                            {{$booking->formatted_check_in_time}}
                        </p>
                        <p>
                            <span style="font-weight: bold">Stay Time:</span>
                            {{$booking->check_in_hours}} hours
                        </p>
                    @else
                        ---
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Price BreakDown -->
    <div>
        <h5 style="margin: 5px 0px; font-size: medium">Price breakdown</h5>
        <div class="priceBreakDown">
            <table class="items" style="width: 100%;">
                <tbody>
                <tr style="display: flex; flex-direction: row; align-items: center; width: 100%; justify-content: space-between;">
                    <td style="padding: 5px">
                        @foreach($booking->roomDetails as $room_detail)
                            <p>{{$room_detail['room_name']}} X {{$room_detail['roomsCount']}} rooms
                                + {{$room_detail['guestsCount']}} Guests
                            </p>
                        @endforeach
                    </td>
                    <td class="price">₹{{$booking->room_rate}}</td>
                </tr>

                <tr style="display: flex; flex-direction: row; align-items: center; width: 100%; justify-content: space-between;">
                    <td style="padding: 5px">Extra Guests Charge</td>
                    <td class="price">₹{{$booking->extra_guest_charge}}</td>
                </tr>
                <tr style="display: flex; flex-direction: row; align-items: center; width: 100%; justify-content: space-between;">
                    <td style="padding: 5px">Platform Fee</td>
                    <td class="price">₹{{$booking->platform_fee}}</td>
                </tr>
                <tr style="display: flex; flex-direction: row; align-items: center; width: 100%; justify-content: space-between;">
                    <td style="padding: 5px">Convenience Fee</td>
                    <td class="price">₹{{$booking->convenience_fee}}</td>
                </tr>
                <tr style="display: flex; font-size: large; flex-direction: row; align-items: center; width: 100%; justify-content: space-between;">
                    <td class="total price" style="text-align: start">Total</td>
                    <td class="total price">₹{{$booking->amount}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<footer>
    <div class="flex-col">
        <p style="margin: 20px auto; color: gray; font-weight: bold; text-align: center">
            Thank you for your visit!
        </p>
        <p style="margin: 20px auto; color: gray; text-align: center">
            This bill is a computer generated, it does not have any signature or
            stamp requirement
        </p>
    </div>
</footer>
</body>
</html>
