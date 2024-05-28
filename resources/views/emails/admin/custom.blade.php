@extends('layouts.master')

@section('content')
    <x-email.header :logo="$logo" />

    <table class="row row-2" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0"
                        role="presentation"
                        style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 680px; margin: 0 auto;"
                        width="680">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="100%"
                                    style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 48px; padding-left: 48px; padding-right: 48px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">

                                    <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0"
                                        cellspacing="0" role="presentation"
                                        style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-bottom:10px;padding-top:10px;">
                                                <div
                                                    style="color:#101112;direction:ltr;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:left;mso-line-height-alt:19.2px;">
                                                    <p style="margin: 0;">
                                                        {!! $custom_message !!}
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="paragraph_block block-4" width="100%" border="0" cellpadding="0"
                                        cellspacing="0" role="presentation"
                                        style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-top:16px;">
                                                <div
                                                    style="color:#666666;direction:ltr;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:12px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:left;mso-line-height-alt:14.399999999999999px;">
                                                    <p style="margin: 0;">Please disregard this message if you are not the
                                                        intended recipient. Thank you.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
