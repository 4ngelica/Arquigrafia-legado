/**
 * This class is responsable for Math operations
 */

class MathController {
  /**
   * Decimal adjustment of a number.
   *
   * @param {String}  type  The type of adjustment.
   * @param {Number}  value The number.
   * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
   * @returns {Number}      The adjusted value.
   */
  static decimalAdjust(type, value, exp) {
    // If the exp is undefined or zero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // If the value is not a number or the exp is not an integer...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  static ceil10(value, exp) {
    return MathController.decimalAdjust('ceil', value, exp);
  }

  static round10(value, exp) {
    return MathController.decimalAdjust('round', value, exp);
  }

  static floor10(value, exp) {
    return MathController.decimalAdjust('floor', value, exp);
  }

  static isEven(number) {
    if (number % 2 === 0) {
      return true;
    }

    return false;
  }
}

export default MathController;
