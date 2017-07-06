define(function() {
	return function(child, parent) {
		if (typeof parent != 'function') {
			parent = child;
			child = function() {
				if (child.prototype.constructor != child) {
					child.prototype.constructor.apply(this, arguments);
				} else {
					parent.apply(this, arguments);
				}
			};
		}
		function Ctor() {
			this.constructor = child;
		}
		Ctor.prototype = parent.prototype;
		child.prototype = new Ctor();
		child.__super__ = parent.prototype;
		child.superClass = parent;
		return child;
	};
});

