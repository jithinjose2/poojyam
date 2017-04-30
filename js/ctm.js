function cyr_translit(a) {
    return to_cyrillic(a)
}
function getConversionHash() {
    if (conversionHash == undefined) {
        var opr = "{" + vowels + roman + chill + swaram + numerals + conjuncts + caps + others;
        for (var consonant in consonants) {
            opr += '"' + consonant + 'a":"' + consonant + 'ാ",';
            opr += '"' + consonant + 'e":"' + consonant + 'േ",';
            opr += '"' + consonant + 'i":"' + consonant + 'ൈ",';
            opr += '"' + consonant + 'o":"' + consonant + 'ോ",';
            opr += '"' + consonant + 'u":"' + consonant + 'ൗ",'
        }
        for (var chk in chillaksharam) {
            opr += '"' + chk + 'a":"' + chillaksharam[chk] + '",';
            opr += '"' + chk + 'e":"' + chillaksharam[chk] + 'െ",';
            opr += '"' + chk + 'i":"' + chillaksharam[chk] + 'ി",';
            opr += '"' + chk + 'o":"' + chillaksharam[chk] + 'ൊ",';
            opr += '"' + chk + 'u":"' + chillaksharam[chk] + 'ു",';
            opr += '"' + chk + 'A":"' + chillaksharam[chk] + 'ാ",';
            opr += '"' + chk + 'E":"' + chillaksharam[chk] + 'േ",';
            opr += '"' + chk + 'I":"' + chillaksharam[chk] + 'ീ",';
            opr += '"' + chk + 'O":"' + chillaksharam[chk] + 'ോ",';
            opr += '"' + chk + 'U":"' + chillaksharam[chk] + 'ൂ",';
            opr += '"' + chk + 'Y":"' + chillaksharam[chk] + 'ൈ",';
            opr += '"' + chk + 'r":"' + chillaksharam[chk] + '്ര്",';
            opr += '"' + chk + 'y":"' + chillaksharam[chk] + '്യ്",';
            opr += '"' + chk + 'v":"' + chillaksharam[chk] + '്വ്",';
            opr += '"' + chk + 'w":"' + chillaksharam[chk] + '്വ്",';
            opr += '"' + chk + '~":"' + chillaksharam[chk] + '്\\u200C",'
        }
        opr += ZWNJ + "}";
        conversionHash = eval("(" + opr + ")");
        maxcyrlength = 6
    }
    return conversionHash
}
function to_cyrillic(x, C, k) {
    if (x == undefined || x === "" || x === null) {
        return x
    }
    if (C == undefined) {
        C = new String
    }
    var b = getConversionHash();
    var q = 0;
    while (q < x.length) {
        var D = Math.min(maxcyrlength, x.length - q);
        var j = undefined;
        var B;
        while (D > 0) {
            B = x.substr(q, D);
            j = b[B];
            if (j != undefined) {
                break
            } else {
                D--
            }
        }
        if (k != undefined) {
            k[k.length] = B
        }
        if (j == undefined) {
            C += B;
            q++
        } else {
            var A = j;
            if (B.toLowerCase() == B.toUpperCase() && j.length > 1 && j[1] && A.toUpperCase() != A.toLowerCase()) {
                var w = C.length == 0 ? null : C.substr(C.length - 1, 1);
                var m = !w || !getTranslitString(w);
                var z = !m && w == w.toUpperCase();
                if (m || !z) {
                    C += A.toLowerCase();
                    z = false
                } else {
                    var v = " ";
                    if (q + D < x.length) {
                        v = x.substr(q + D, 1)
                    }
                    if (v != v.toUpperCase() && v == v.toLowerCase()) {
                        C += A.toLowerCase()
                    } else {
                        if (v == v.toUpperCase() && v != v.toLowerCase()) {
                            C += A.toUpperCase()
                        } else {
                            var g = C.length == 1 ? null : C.substr(C.length - 2, 1);
                            var y = !g || !getTranslitString(g);
                            if (!y && g == g.toUpperCase()) {
                                C += A.toUpperCase()
                            } else {
                                C += A.toLowerCase()
                            }
                        }
                    }
                }
            } else {
                if (B.toLowerCase() == B.toUpperCase() && (j.length < 2 || !j[1])) {
                    C += A
                } else {
                    if (B != B.toLowerCase()) {
                        C += A.toUpperCase()
                    } else {
                        C += A.toLowerCase()
                    }
                }
            }
            q += D
        }
    }
    return C
}
function convertIt(c, a) {
    var d = "";
    for (var b = 0; b < c.length; b++) {
        d = a(d + c[b])
    }
    return a(d)
}
function initTranslit() {
    if (translitHash == undefined) {
        translitHash = new Array;
        for (var b = 0; b < conversionHash.length; b++) {
            var a = conversionHash[b][1];
            if (conversionHash[b][0].toUpperCase() != conversionHash[b][0].toLowerCase()) {
                a = a.toUpperCase()
            }
            if (translitHash[a] == undefined) {
                translitHash[a] = conversionHash[b][0]
            }
        }
    }
}
function replaceValue(f, b, g) {
    if (g == undefined) {
        g = 0
    }
    if (isExplorer()) {
        var d = document.selection.createRange();
        d.moveStart("character", -g);
        d.text = b;
        d.collapse(false);
        d.select()
    } else {
        var a = f.scrollTop;
        var c = f.selectionStart;
        f.value = f.value.substring(0, f.selectionStart - g) + b + f.value.substring(f.selectionEnd, f.value.length);
        f.scrollTop = a;
        f.selectionStart = c + b.length - g;
        f.selectionEnd = c + b.length - g
    }
}
function positionIsEqual(a) {
    return this.position == a.position
}
function Position(a) {
    if (a.selectionStart != undefined) {
        this.position = a.selectionStart
    } else {
        if (document.selection && document.selection.createRange()) {
            this.position = document.selection.createRange()
        }
    }
    this.isEqual = positionIsEqual
}
function resetState() {
    this.position = new Position(this.node);
    this.transBuffer = "";
    this.cyrBuffer = ""
}
function StateObject(a) {
    this.node = a;
    this.reset = resetState;
    this.cyrBuffer = "";
    this.transBuffer = "";
    this.position = new Position(a)
}
function isExplorer() {
    return false
}
function pressedKey(a) {
    if (isExplorer()) {
        return a.keyCode
    } else {
        return a.which
    }
}
function transliterateKey(d) {
    if (d == undefined) {
        d = window.event
    }
    var b = null;
    if (d.target) {
        b = d.target
    } else {
        if (d.srcElement) {
            b = d.srcElement
        }
    }
    var f = stateHash[b];
    if (f == null) {
        f = new StateObject(b);
        stateHash[b] = f
    }
    if (pressedKey(d) > 20 && !d.ctrlKey && !d.altKey && !d.metaKey) {
        var c = String.fromCharCode(pressedKey(d));
        var a = process_translit(f, c);
        if (c != a.out || a.replace != 0) {
            if (isExplorer()) {
                d.returnValue = false
            } else {
                d.preventDefault()
            }
            replaceValue(b, a.out, a.replace);
            f.position = new Position(b)
        }
    }
}
function TranslitResult() {
    this.out = "";
    this.replace = 0
}
function process_translit(f, b) {
    if (!f.position.isEqual(new Position(f.node))) {
        f.reset()
    }
    var h = new TranslitResult;
    var d = getBackBuffer(f.node, f.cyrBuffer.length, 2);
    var a = new Array;
    f.transBuffer = f.transBuffer + b;
    var c = to_cyrillic(f.cyrBuffer + b, d, a);
    c = c.substr(d.length);
    h.out = c;
    for (var g = 0; g < Math.min(f.cyrBuffer.length, h.out.length); g++) {
        if (f.cyrBuffer.substr(g, 1) != h.out.substr(g, 1)) {
            h.replace = f.cyrBuffer.length - g;
            h.out = h.out.substr(g);
            break
        }
    }
    if (h.replace == 0) {
        if (h.out.length < f.cyrBuffer.length) {
            h.replace = f.cyrBuffer.length - h.out.length
        }
        h.out = h.out.substr(Math.min(f.cyrBuffer.length, h.out.length))
    }
    if (a.length > 0 && a[a.length - 1] == h.out.substr(h.out.length - 1)) {
        f.reset()
    } else {
        while (f.transBuffer.length > maxcyrlength) {
            f.transBuffer = f.transBuffer.substr(a[0].length);
            a.shift();
            c = c.substr(1)
        }
        f.cyrBuffer = c
    }
    return h
}
function getBackBuffer(d, b, f) {
    if (isExplorer()) {
        var c = document.selection.createRange();
        c.moveStart("character", -b);
        var a = c.text.substr(-f);
        if (!a) {
            a = ""
        }
        return a
    } else {
        return d.value.substring(0, d.selectionStart - b).substr(-f)
    }
}
function getSelectedNode() {
    if (document.activeElement) {
        return document.activeElement
    } else {
        if (window.getSelection && window.getSelection() && window.getSelection().rangeCount > 0) {
            var a = window.getSelection().getRangeAt(0);
            if (a.startContainer && a.startContainer.childNodes && a.startContainer.childNodes.length > a.startOffset) {
                return a.startContainer.childNodes[a.startOffset]
            }
        }
    }
    return null
}
function toggleCyrMode() {
    var a = getSelectedNode();
    if (a) {
        if (stateHash[a]) {
            if (removeKeyEventListener(a)) {
                delete stateHash[a]
            }
        } else {
            if (addKeyEventListener(a)) {
                stateHash[a] = new StateObject(a)
            }
        }
    }
}
function addKeyEventListener(a) {
    if (a.addEventListener) {
        a.addEventListener("keypress", transliterateKey, false)
    } else {
        if (a.attachEvent) {
            a.attachEvent("onkeypress", transliterateKey)
        } else {
            return false
        }
    }
    return true
}
function removeKeyEventListener(a) {
    if (a.removeEventListener) {
        a.removeEventListener("keypress", transliterateKey, false)
    } else {
        if (a.detachEvent) {
            a.detachEvent("onkeypress", transliterateKey)
        } else {
            return false
        }
    }
    return true
}
function getSelectedText() {
    if (isExplorer()) {
        return document.selection.createRange().text
    } else {
        var a = getSelectedNode();
        if (a && a.value && a.selectionStart != undefined && a.selectionEnd != undefined) {
            return a.value.substring(a.selectionStart, a.selectionEnd)
        }
    }
    return ""
}
var consonants = {
    "ക": "ക",
    "ഖ": "ഖ",
    "ഗ": "ഗ",
    "ഘ": "ഘ",
    "ങ": "ങ",
    "ച": "ച",
    "ഛ": "ഛ",
    "ജ": "ജ",
    "ഝ": "ഝ",
    "ഞ": "ഞ",
    "ട": "ട",
    "ഠ": "ഠ",
    "ഡ": "ഡ",
    "ഢ": "ഢ",
    "ണ": "ണ",
    "ത": "ത",
    "ഥ": "ഥ",
    "ദ": "ദ",
    "ധ": "ധ",
    "ന": "ന",
    "പ": "പ",
    "ഫ": "ഫ",
    "ബ": "ബ",
    "ഭ": "ഭ",
    "മ": "മ",
    "യ": "യ",
    "ര": "ര",
    "ല": "ല",
    "വ": "വ",
    "ശ": "ശ",
    "ഷ": "ഷ",
    "സ": "സ",
    "ഹ": "ഹ",
    "ള": "ള",
    "ഴ": "ഴ",
    "റ": "റ",
    "റ്റ": "റ്റ"
};
var chillaksharam = {
    "ണ്": "ണ",
    "ന്": "ന",
    "ം": "മ",
    "ര്": "ര",
    "ല്": "ല",
    "ള്": "ള",
    "്\\u200D": ""
};
var vowels = '"്a":"","്e":"െ","്i":"ി","്o":"ൊ","്u":"ു","്A":"ാ","്E":"േ","്I":"ീ","്O":"ോ","്U":"ൂ","്Y":"ൈ","െe":"ീ","ൊo":"ൂ","ിi":"ീ","ിe":"ീ","ുu":"ൂ","ുo":"ൂ","്r":"്ര്",';
var roman = '"k":"ക്","ക്h":"ഖ്","g":"ഗ്","ഗ്h":"ഘ്","ന്g":"ങ്","c":"ക്\\u200D","ക്\\u200Dh":"ച്","ച്h":"ഛ്","j":"ജ്","ജ്h":"ഝ്","ന്j":"ഞ്","ന്h":"ഞ്","T":"ട്","ട്h":"ഠ്","D":"ഡ്","ഡ്h":"ഢ്","റ്റ്h":"ത്","ത്h":"ഥ്","d":"ദ്","ദ്h":"ധ്","p":"പ്","പ്h":"ഫ്","f":"ഫ്","b":"ബ്","ബ്h":"ഭ്","y":"യ്","v":"വ്","w":"വ്","z":"ശ്","S":"ശ്","സ്h":"ഷ്","s":"സ്","h":"ഹ്","ശ്h":"ഴ്","x":"ക്ഷ്","R":"റ്","t":"റ്റ്",';
var chill = '"N":"ണ്","n":"ന്","m":"ം","r":"ര്","l":"ല്","L":"ള്",';
var swaram = '"a":"അ","അa":"ആ","A":"ആ","e":"എ","E":"ഏ","എe":"ഈ","i":"ഇ","ഇi":"ഈ","ഇe":"ഈ","അi":"ഐ","I":"ഐ","o":"ഒ","ഒo":"ഊ","O":"ഓ","അu":"ഔ","ഒu":"ഔ","u":"ഉ","ഉu":"ഊ","U":"ഊ","H":"ഃ","റ്h":"ഋ","ര്^":"ഋ","ഋ^":"ൠ","ല്^":"ഌ","ഌ^":"ൡ",';
var numerals = "";
var conjuncts = '"ന്t":"ന്റ്","ന്റ്h":"ന്ത്","ന്k":"ങ്ക്","ന്n":"ന്ന്","ണ്N":"ണ്ണ്","ള്L":"ള്ള്","ല്l":"ല്ല്","ംm":"മ്മ്","ന്m":"ന്മ്","ന്ന്g":"ങ്ങ്","ന്d":"ന്ദ്","ണ്m":"ണ്മ്","ല്p":"ല്പ്","ംp":"മ്പ്","റ്റ്t":"ട്ട്","ന്T":"ണ്ട്","ണ്T":"ണ്ട്","്ര്^":"ൃ","ന്c":"ന്\\u200D","ന്\\u200Dh":"ഞ്ച്","ണ്D":"ണ്ഡ്",';
var others = '"്L":"്ല്","~":"്\\u200C","്~":"്\\u200C","\\u200C~":"\\u200C","ം~":"മ്","ക്\\u200Dc":"ക്ക്\\u200D","ക്ക്\\u200Dh":"ച്ച്","q":"ക്യൂ","$":"\\u20B9",';
var caps = '"B":"ബ്ബ്","C":"ക്ക്\\u200D","F":"ഫ്","G":"ഗ്ഗ്","J":"ജ്ജ്","K":"ക്ക്","M":"മ്മ്","P":"പ്പ്","Q":"ക്യൂ","V":"വ്വ്","W":"വ്വ്","X":"ക്ഷ്","Y":"യ്യ്","Z":"ശ്ശ്",';
var ZWNJ = '"_":"\\u200C"';
var conversionHash = undefined;
var maxcyrlength = 0;
var translitHash = undefined;
var stateHash = new Array;
(function(b) {
    b.cookie = function(s, r, q) {
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(r)) || r === null || r === undefined)) {
            q = b.extend({}, q);
            if (r === null || r === undefined) {
                q.expires = -1
            }
            if (typeof q.expires === "number") {
                var o = q.expires,
                    n = q.expires = new Date;
                n.setDate(n.getDate() + o)
            }
            r = String(r);
            return document.cookie = [encodeURIComponent(s), "=", q.raw ? r : encodeURIComponent(r), q.expires ? "; expires=" + q.expires.toUTCString() : "", q.path ? "; path=" + q.path : "", q.domain ? "; domain=" + q.domain : "", q.secure ? "; secure" : ""].join("")
        }
        q = r || {};
        var m = q.raw ?
        function(c) {
            return c
        } : decodeURIComponent;
        var l = document.cookie.split("; ");
        for (var k = 0, a; a = l[k] && l[k].split("="); k++) {
            if (m(a[0]) === s) {
                return m(a[1] || "")
            }
        }
        return null
    }
})(jQuery);
$(document).ready(function() {
    if($("body").attr("lan")=='mal'){
        DICT = "ml_ml";
        switchDict(DICT);
        $("#search .tab").click(function() {
            switchDict($(this).data("dict"));
            return false
        });
    }
});

function switchDict(b) {
    DICT = b;
    if (b == "ml_ml") {
        $("#chat_text").unbind("keypress").bind("keypress", function(c) {
            transliterateKey(c)
        })
    } else {
        $("#chat_text").unbind("keypress")
    }
    var a = $("#chat_text").val();
    $("#chat_text").focus().val("").val(a)
}