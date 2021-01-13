<?php

/**
 * @category Trans
 * @package  Trans_Integration_Exception
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hariadi <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */

namespace Trans\Integration\Utility;


class CurlError
{
    public const ERROR = [
        1  => "CURL_UNSUPPORTED_PROTOCOL",
        2  => "CURL_FAILED_INIT",
        3  => "CURL_URL_MALFORMAT",
        4  => "CURL_URL_MALFORMAT_USER",
        5  => "CURL_COULDNT_RESOLVE_PROXY",
        6  => "CURL_COULDNT_RESOLVE_HOST",
        7  => "CURL_COULDNT_CONNECT",
        8  => "CURL_FTP_WEIRD_SERVER_REPLY",
        9  => "CURL_FTP_ACCESS_DENIED",
        10 => "CURL_FTP_USER_PASSWORD_INCORRECT",
        11 => "CURL_FTP_WEIRD_PASS_REPLY",
        12 => "CURL_FTP_WEIRD_USER_REPLY",
        13 => "CURL_FTP_WEIRD_PASV_REPLY",
        14 => "CURL_FTP_WEIRD_227_FORMAT",
        15 => "CURL_FTP_CANT_GET_HOST",
        16 => "CURL_FTP_CANT_RECONNECT",
        17 => "CURL_FTP_COULDNT_SET_BINARY",
        18 => "CURL_FTP_PARTIAL_FILE or CURL_PARTIAL_FILE",
        19 => "CURL_FTP_COULDNT_RETR_FILE",
        20 => "CURL_FTP_WRITE_ERROR",
        21 => "CURL_FTP_QUOTE_ERROR",
        22 => "CURL_HTTP_NOT_FOUND or CURL_HTTP_RETURNED_ERROR",
        23 => "CURL_WRITE_ERROR",
        24 => "CURL_MALFORMAT_USER",
        25 => "CURL_FTP_COULDNT_STOR_FILE",
        26 => "CURL_READ_ERROR",
        27 => "CURL_OUT_OF_MEMORY",
        28 => "CURL_OPERATION_TIMEDOUT or CURL_OPERATION_TIMEOUTED",
        29 => "CURL_FTP_COULDNT_SET_ASCII",
        30 => "CURL_FTP_PORT_FAILED",
        31 => "CURL_FTP_COULDNT_USE_REST",
        32 => "CURL_FTP_COULDNT_GET_SIZE",
        33 => "CURL_HTTP_RANGE_ERROR",
        34 => "CURL_HTTP_POST_ERROR",
        35 => "CURL_SSL_CONNECT_ERROR",
        36 => "CURL_BAD_DOWNLOAD_RESUME or CURL_FTP_BAD_DOWNLOAD_RESUME",
        37 => "CURL_FILE_COULDNT_READ_FILE",
        38 => "CURL_LDAP_CANNOT_BIND",
        39 => "CURL_LDAP_SEARCH_FAILED",
        40 => "CURL_LIBRARY_NOT_FOUND",
        41 => "CURL_FUNCTION_NOT_FOUND",
        42 => "CURL_ABORTED_BY_CALLBACK",
        43 => "CURL_BAD_FUNCTION_ARGUMENT",
        44 => "CURL_BAD_CALLING_ORDER",
        45 => "CURL_HTTP_PORT_FAILED",
        46 => "CURL_BAD_PASSWORD_ENTERED",
        47 => "CURL_TOO_MANY_REDIRECTS",
        48 => "CURL_UNKNOWN_TELNET_OPTION",
        49 => "CURL_TELNET_OPTION_SYNTAX",
        50 => "CURL_OBSOLETE",
        51 => "CURL_SSL_PEER_CERTIFICATE",
        52 => "CURL_GOT_NOTHING",
        53 => "CURL_SSL_ENGINE_NOTFOUND",
        54 => "CURL_SSL_ENGINE_SETFAILED",
        55 => "CURL_SEND_ERROR",
        56 => "CURL_RECV_ERROR",
        57 => "CURL_SHARE_IN_USE",
        58 => "CURL_SSL_CERTPROBLEM",
        59 => "CURL_SSL_CIPHER",
        60 => "CURL_SSL_CACERT",
        61 => "CURL_BAD_CONTENT_ENCODING",
        62 => "CURL_LDAP_INVALID_URL",
        63 => "CURL_FILESIZE_EXCEEDED",
        64 => "CURL_FTP_SSL_FAILED",
        79 => "CURL_SSH"
    ];    
}
