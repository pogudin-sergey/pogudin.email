/**
 * Created by Pogudin Sergey on 04.11.2015.
 */

var EProtected = {
    mychar1: 59+5,
    mychar2: ":",
    mychar3: "il",
    mychar4: "to",
    setEP: function(varname, showto) {
        BX.ready(function() {
            var param = (showto) ?
                {
                    html: EProtected.getSecond(varname) + "" + EProtected.getThird(varname),
                    props: {
                        href: EProtected.getFirst(varname) + EProtected.getSecond(varname) + "" + EProtected.getThird(varname)
                    }
                } : {
                    html: EProtected.getSecond(varname) + "" + EProtected.getThird(varname)
                };

            BX.adjust(BX(varname), param);
        });
    },
    getFirst: function (varname) {
        return "ma" + this.mychar3 + this.mychar4 + this.mychar2;
    },
    getSecond: function (varname) {
        var mF = eval("e"+varname+"F");
        return this.base64_decode(mF) + "" + String.fromCharCode(this.mychar1);
    },
    getThird: function (varname) {
        var mS = eval("e"+varname+"S");
        return this.base64_decode(mS);
    },
    base64_decode: function(data) {
        var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
        var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            dec = '',
            tmp_arr = [];

        if (!data) {
            return data;
        }

        data += '';

        do { // unpack four hexets into three octets using index points in b64
            h1 = b64.indexOf(data.charAt(i++));
            h2 = b64.indexOf(data.charAt(i++));
            h3 = b64.indexOf(data.charAt(i++));
            h4 = b64.indexOf(data.charAt(i++));

            bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

            o1 = bits >> 16 & 0xff;
            o2 = bits >> 8 & 0xff;
            o3 = bits & 0xff;

            if (h3 == 64) {
                tmp_arr[ac++] = String.fromCharCode(o1);
            } else if (h4 == 64) {
                tmp_arr[ac++] = String.fromCharCode(o1, o2);
            } else {
                tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
            }
        } while (i < data.length);

        dec = tmp_arr.join('');

        return dec.replace(/\0+$/, '');
    }
};
