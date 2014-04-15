app.modules.PageUserNav = Backbone.View.extend({
  render: function() {
    this.$el.html(Mustache.render(app.mustache['page-user-nav'], this.model.attributes));
  }
});

app.modules.PageBaseView = Backbone.View.extend({
  renderNavigation: function(user) {
    var el = this.$el.find('.page-base-nav');
    if(!this.user_nav) {
      this.user_nav = new app.modules.PageUserNav({
        el: el,
        model: new Backbone.Model(app.data.user)
      });
    }
    this.user_nav.render();
  },
  render: function() {
    this.el.innerHTML += app.mustache['page-base'];
    this.$elPage = this.$el.find('.content-display-wrapper');
    this.renderPage();
    this.renderNavigation();
    this.$el.find('.loading-background').addClass('hide');
    this.$el.find('.content-display').removeClass('hide');

    // initialize resize event
    var self = this;
    $(window).resize(function() {
      self.resize();
    });
    self.resize();
  },
  // abstract void
  resize: function() {},
  // abstract void
  renderPage: function() {}
});