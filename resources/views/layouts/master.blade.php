<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: inherit !important;
        }

        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
        }

        p {
            line-height: inherit
        }

        .desktop_hide,
        .desktop_hide table {
            mso-hide: all;
            display: none;
            max-height: 0px;
            overflow: hidden;
        }

        .image_block img+div {
            display: none;
        }

        .menu_block.desktop_hide .menu-links span {
            mso-hide: all;
        }

        @media (max-width:700px) {
            .desktop_hide table.icons-outer {
                display: inline-table !important;
            }

            .desktop_hide table.icons-inner,
            .row-2 .column-1 .block-3.button_block .alignment a,
            .row-2 .column-1 .block-3.button_block .alignment div,
            .social_block.desktop_hide .social-table {
                display: inline-block !important;
            }

            .icons-inner {
                text-align: center;
            }

            .icons-inner td {
                margin: 0 auto;
            }

            .mobile_hide {
                display: none;
            }

            .row-content {
                width: 100% !important;
            }

            .stack .column {
                width: 100%;
                display: block;
            }

            .mobile_hide {
                min-height: 0;
                max-height: 0;
                max-width: 0;
                overflow: hidden;
                font-size: 0px;
            }

            .desktop_hide,
            .desktop_hide table {
                display: table !important;
                max-height: none !important;
            }

            .row-2 .column-1 .block-2.paragraph_block td.pad>div {
                text-align: left !important;
                font-size: 14px !important;
            }

            .row-2 .column-1 .block-1.heading_block h1,
            .row-2 .column-1 .block-3.button_block .alignment {
                text-align: left !important;
            }

            .row-2 .column-1 .block-1.heading_block h1 {
                font-size: 20px !important;
            }

            .row-2 .column-1 .block-4.paragraph_block td.pad>div {
                text-align: justify !important;
                font-size: 10px !important;
            }

            .row-2 .column-1 .block-3.button_block a,
            .row-2 .column-1 .block-3.button_block div,
            .row-2 .column-1 .block-3.button_block span {
                font-size: 14px !important;
                line-height: 28px !important;
            }

            .row-3 .column-1 .block-1.icons_block .pad,
            .row-5 .column-1 .block-2.menu_block .alignment {
                text-align: center !important;
            }

            .row-3 .column-1 .block-1.icons_block td.pad {
                padding: 10px 24px !important;
            }

            .row-3 .column-2 .block-1.paragraph_block td.pad>div {
                text-align: left !important;
                font-size: 16px !important;
            }

            .row-5 .column-1 .block-2.menu_block td.pad {
                padding: 8px !important;
            }

            .row-5 .column-1 .block-2.menu_block .menu-links a,
            .row-5 .column-1 .block-2.menu_block .menu-links span {
                font-size: 14px !important;
            }

            .row-2 .column-1 {
                padding: 0 24px 48px !important;
            }

            .row-3 .column-1 {
                padding: 16px 16px 8px !important;
            }

            .row-3 .column-2 {
                padding: 0 24px 16px !important;
            }

            .row-5 .column-1 {
                padding: 32px 16px 48px !important;
            }
        }
    </style>
</head>

<body style="background-color: #f8f6ff; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
    <table class="nl-container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f8f6ff; background-image: none; background-position: top left; background-size: auto; background-repeat: no-repeat;">
        <tbody>
            <tr>
                <td>
                    @yield('content')
                    <x-email.footer />
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
