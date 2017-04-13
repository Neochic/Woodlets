/*
 * Initializes jQuery UI Datepicker for
 * Datepicker field type.
 */

/* globals document */

define([
    'jquery',
    'moment',
    'debounce',
    'inputmask',
    'native-change',
    'jquery-inputmask',
    'inputmask-date-extensions',
    'bootstrap-datepicker',
    'jquery-caret'
], function($, moment, debounce, Inputmask, nativeChange) {

    var $document = $(document);

    Inputmask.extendAliases({
        custom01: {
            mask: "1.2.y - h:s",
            placeholder: "TT.MM.JJJJ - SS:MM",
            alias: "dd/mm/yyyy",
            regex: {
                hrspre: new RegExp("[012]"), //hours pre
                hrs24: new RegExp("2[0-4]|1[3-9]"),
                hrs: new RegExp("[01][0-9]|2[0-4]"), //hours
                ampm: new RegExp("^[a|p|A|P][m|M]"),
                mspre: new RegExp("[0-5]"), //minutes/seconds pre
                ms: new RegExp("[0-5][0-9]")
            },
            separator: ".",
            timeseparator: ":",
            hourFormat: "24", // or 12
            definitions: {
                "h": { //hours
                    validator: function (chrs, maskset, pos, strict, opts) {
                        if (opts.hourFormat === "24") {
                            if (parseInt(chrs, 10) === 24) {
                                maskset.buffer[pos - 1] = "0";
                                maskset.buffer[pos] = "0";
                                return {
                                    "refreshFromBuffer": {
                                        start: pos - 1,
                                        end: pos
                                    },
                                    "c": "0"
                                };
                            }
                        }

                        var isValid = opts.regex.hrs.test(chrs);
                        if (!strict && !isValid) {
                            if (chrs.charAt(1) === opts.timeseparator || "-.:".indexOf(chrs.charAt(1)) !== -1) {
                                isValid = opts.regex.hrs.test("0" + chrs.charAt(0));
                                if (isValid) {
                                    maskset.buffer[pos - 1] = "0";
                                    maskset.buffer[pos] = chrs.charAt(0);
                                    pos++;
                                    return {
                                        "refreshFromBuffer": {
                                            start: pos - 2,
                                            end: pos
                                        },
                                        "pos": pos,
                                        "c": opts.timeseparator
                                    };
                                }
                            }
                        }

                        if (isValid && opts.hourFormat !== "24" && opts.regex.hrs24.test(chrs)) {

                            var tmp = parseInt(chrs, 10);

                            if (tmp === 24) {
                                maskset.buffer[pos + 5] = "a";
                                maskset.buffer[pos + 6] = "m";
                            } else {
                                maskset.buffer[pos + 5] = "p";
                                maskset.buffer[pos + 6] = "m";
                            }

                            tmp = tmp - 12;

                            if (tmp < 10) {
                                maskset.buffer[pos] = tmp.toString();
                                maskset.buffer[pos - 1] = "0";
                            } else {
                                maskset.buffer[pos] = tmp.toString().charAt(1);
                                maskset.buffer[pos - 1] = tmp.toString().charAt(0);
                            }

                            return {
                                "refreshFromBuffer": {
                                    start: pos - 1,
                                    end: pos + 6
                                },
                                "c": maskset.buffer[pos]
                            };
                        }

                        return isValid;
                    },
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, maskset, pos, strict, opts) {
                            var isValid = opts.regex.hrspre.test(chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.hrs.test("0" + chrs);
                                if (isValid) {
                                    maskset.buffer[pos] = "0";
                                    pos++;
                                    return {
                                        "pos": pos
                                    };
                                }
                            }
                            return isValid;
                        },
                        cardinality: 1
                    }]
                },
                "s": { //seconds || minutes
                    validator: "[0-5][0-9]",
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, maskset, pos, strict, opts) {
                            var isValid = opts.regex.mspre.test(chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.ms.test("0" + chrs);
                                if (isValid) {
                                    maskset.buffer[pos] = "0";
                                    pos++;
                                    return {
                                        "pos": pos
                                    };
                                }
                            }
                            return isValid;
                        },
                        cardinality: 1
                    }]
                },
                "t": { //am/pm
                    validator: function (chrs, maskset, pos, strict, opts) {
                        return opts.regex.ampm.test(chrs + "m");
                    },
                    casing: "lower",
                    cardinality: 1
                }
            },
            insertMode: false,
            autoUnmask: false
        }
    });

    function init(form) {

        var $form = $(form);
        var linkInputFunctions = [];

        $form.find('.neochic-woodlets-datetime:not(.initialized)').each(function(i, e) {
            $(this).addClass('initialized');

            var inputHidden = $(e);
            var datepickerElement = inputHidden.next();
            var inputVisible = datepickerElement.find("input");
            var type = inputHidden.data("type");

            if(isNativeUnsupported(inputHidden)) {

                var saveFormat = inputHidden.data("save-format");
                var displayFormat = inputHidden.data("display-format");
                var inputmaskFormat = inputHidden.data("inputmask-format");
                var pickerToggle = inputVisible.next();
                var container = inputHidden.parent();
                var pickerVisible = false;

                container.addClass("neochic-woodlets-datetime-fallback");

                var getMomentFromHiddenVal = function(val) {
                    var retVal;
                    if (type !== "time") {
                        retVal = moment(val, moment.ISO_8601);
                    } else {
                        var tmp = val;
                        if (tmp) {
                            tmp = tmp.split(":");
                            retVal = moment();
                            retVal.hours(tmp[0]);
                            retVal.minutes(tmp[1]);
                        } else {
                            retVal = moment("");
                        }
                    }
                    return retVal;
                };

                var startVal = getMomentFromHiddenVal(inputHidden.val());

                datepickerElement.datetimepicker({
                    defaultDate: startVal.isValid() ? startVal : null,
                    format: displayFormat
                });

                inputVisible.off("focus blur keydown keyup");

                var datePicker = datepickerElement.data("DateTimePicker");
                inputHidden.data("picker-ele", datepickerElement);

                var lastSelectRange = null;

                inputVisible.inputmask(inputmaskFormat, {
                    oncomplete: function(){
                        lastSelectRange = inputVisible.range();
                        var tmp = moment(inputVisible.val(), displayFormat);
                        if(tmp.isValid() && !tmp.isSame(datePicker.date())) {
                            //prevent datepicker from claiming focus without triggering a change event
                            datePicker.date(tmp);
                        }
                    }
                });

                datepickerElement.on("dp.change", function(e) {
                    inputHidden.val(e.date ? e.date.format(saveFormat) : null);
                    if (lastSelectRange !== null) {
                        inputVisible.range(lastSelectRange.start, lastSelectRange.end);
                        lastSelectRange = null;
                    }
                    nativeChange(inputHidden.get(0));
                }).on("dp.show", function(){
                    pickerVisible = true;
                }).on("dp.hide", function(){
                    pickerVisible = false;
                });

                var lastVal = inputHidden.val();
                inputHidden.on("change", function() {
                    var newVal = inputHidden.val();
                    if(lastVal !== newVal) {
                        lastVal = newVal;
                        var tmp = getMomentFromHiddenVal(newVal);
                        if (tmp.isValid()) {
                            datePicker.date(tmp);
                        }
                    }
                });

                pickerToggle.on("keypress", function(e){
                    if(e.which === 13) {
                        datePicker.toggle();
                    }
                });

                $document.on("click focusin", function (e) {
                    var target = $(e.target);

                    var clickOutside = target.parents().addBack().filter(container).length < 1;

                    if (clickOutside) {
                        datePicker.hide();
                    }
                });

                inputVisible.on("focus", function(){
                    inputVisible.selectAll();
                });
            }

            var endswithSelector = inputHidden.data("endswith");
            var endswith = endswithSelector ? $form.find("[data-name=" + endswithSelector + "]") : [];
            if (type !== "time" && endswith.length > 0) {
                linkInputFunctions.push(function () {

                    var setMinDate;
                    var setMaxDate;

                    if(isNativeUnsupported(inputHidden) && isNativeUnsupported(endswith)) {

                        var i1 = inputHidden.data("picker-ele");
                        var i2 = endswith.data("picker-ele");
                        var p1 = i1.data("DateTimePicker");
                        var p2 = i2.data("DateTimePicker");

                        p1.useCurrent(false);
                        p2.useCurrent(false);

                        setMinDate = function(date){
                            p2.minDate(date ? date : false);
                        };

                        setMaxDate = function(date){
                            p1.maxDate(date ? date : false);
                        };

                        i1.on("dp.change", function (e) {
                            setMinDate(e.date);
                        });

                        i2.on("dp.change", function (e) {
                            setMaxDate(e.date);
                        });

                        setMinDate(p1.date());
                        setMaxDate(p2.date());

                    } else {

                        setMinDate = function(val){
                            if (val) {
                                endswith.removeAttr("min");
                            } else {
                                endswith.attr("min", val);
                            }
                        };

                        setMaxDate = function(val){
                            if (val) {
                                inputHidden.removeAttr("min");
                            } else {
                                inputHidden.attr("max", val);
                            }
                        };

                        inputHidden.on("change", function() {
                            setMinDate(inputHidden.val());
                        });

                        endswith.on("change", function() {
                            setMaxDate(endswith.val());
                        });

                        setMinDate(inputHidden.val());
                        setMaxDate(endswith.val());
                    }

                });
            }
        });

        linkInputFunctions.forEach(Function.prototype.call, Function.prototype.call);
    }

    function isNativeUnsupported (input) {
        if (input.data("disable-native")) {
            return true;
        }
        var tmp = input.data("native-unsupported");
        if (tmp !== null) {
            return tmp;
        }
        var checkForNativeSupport = document.createElement("input");
        checkForNativeSupport.setAttribute("type", input.data("type"));
        var nativeUnsupported = checkForNativeSupport.type === "text";
        input.data("native-unsupported", nativeUnsupported);
        return nativeUnsupported;
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
