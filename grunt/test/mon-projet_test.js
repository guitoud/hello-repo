(function($) {
  /*
    ======== A Handy Little QUnit Reference ========
    https://api.qunitjs.com/

    Test methods:
      module(name, {[setup][ ,teardown]})
      test(name, callback)
      expect(numberOfAssertions)
    Test assertions:
      ok(value, [message])
      equal(actual, expected, [message])
      notEqual(actual, expected, [message])
      propEqual(actual, expected, [message])
      notPropEqual(actual, expected, [message])
      strictEqual(actual, expected, [message])
      notStrictEqual(actual, expected, [message])
      throws(block, [expected], [message])
  */

  QUnit.module('jQuery#mon_projet', {
    // This will run before each test in this module.
    beforeEach: function() {
      this.elems = $('#qunit-fixture').children();
    }
  });

  QUnit.test('is chainable', function(assert) {
    assert.expect(1);
    // Not a bad test to run on collection methods.
    assert.strictEqual(this.elems.mon_projet(), this.elems, 'should be chainable');
  });

  QUnit.test('is awesome', function(assert) {
    assert.expect(1);
    assert.strictEqual(this.elems.mon_projet().text(), 'awesome0awesome1awesome2', 'should be awesome');
  });

  QUnit.module('jQuery.mon_projet');

  QUnit.test('is awesome', function(assert) {
    assert.expect(2);
    assert.strictEqual($.mon_projet(), 'awesome.', 'should be awesome');
    assert.strictEqual($.mon_projet({punctuation: '!'}), 'awesome!', 'should be thoroughly awesome');
  });

  QUnit.module(':mon_projet selector', {
    // This will run before each test in this module.
    beforeEach: function() {
      this.elems = $('#qunit-fixture').children();
    }
  });

  QUnit.test('is awesome', function(assert) {
    assert.expect(1);
    // Use propEqual & .get() when comparing jQuery objects.
    assert.propEqual(this.elems.filter(':mon_projet').get(), this.elems.last().get(), 'knows awesome when it sees it');
  });

}(jQuery));
