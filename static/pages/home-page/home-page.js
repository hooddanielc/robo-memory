var RoboMemoryCard = Backbone.View.extend({
  events: {
    'click .flip-container': 'flip'
  },
  flip: function() {
    this.model.set('isFlipped', !this.model.get('isFlipped'));
    this.$el.find('.flip-container').quickFlipper();
  },
  render: function() {
    var self = this;
    this.$el.html(Mustache.render(app.mustache['robo-memory-card'], this.model.attributes));
    this.$el.find('.robo-img').load(function() {
      self.trigger('loaded');
    });
    this.$el.find('.flip-container').quickFlip();
  }
});

// The main controller/view for our game
// responsible for high level action such
// as starting a new game
var RoboMemory = Backbone.View.extend({
  events: {
    'click .finish-game': 'finish_game'
  },
  notify_success: function(success) {
    if(!success) {
      alert('oh no! The robot army has defeated you! Would you like to try again?')
    } else {
      alert("WOW, Great Job! You have defeated the robot army of super smartness. Want to try again?");
    }
    // restart the game regardless
    // if the user rage quit or not
    this.render();
  },
  finish_game: function() {
    var flipped = this.model.get('flipped');
    var unflipped = this.model.get('unflipped');
    var pass = true;
    for(var i = 0; i < flipped.length; i++) {
      if(!flipped[i].model.get('isFlipped')) {
        pass = false;
      }
    }
    for(var i = 0; i < unflipped.length; i++) {
      if(unflipped[i].model.get('isFlipped')) {
        pass = false;
      }
    }
    this.notify_success(pass);
  },
  lockUI: function() {
    this.$el.find('.lock-ui').show();
  },
  unlockUI: function() {
    this.$el.find('.lock-ui').hide();
  },
  show_preview_flip: function() {
    var flip_cards = this.model.get('flipped');
    var self = this;
    for(var i = 0; i < flip_cards.length; i++) {
      (function(card, i) {
        setTimeout(function() {
          card.flip();
          if(i == flip_cards.length - 1) {
            // allow user to choose cards now
            setTimeout(function() {
              for(var i = 0; i < flip_cards.length; i++) {
                flip_cards[i].flip();
              }
              self.unlockUI();
              self.$el.find('.finish-btn').removeClass('hide');
            }, 5000);
          }
        }, i * 100);
      })(flip_cards[i], i);
    }
  },
  show_cards: function() {
    var self = this;
    this.$el.find('.bg').fadeOut();
    this.$el.find('.loading').fadeOut(function() {
      // pick 9 cards at random
      var cards = self.model.get('cards');
      var shuffledCards = _.shuffle(cards);
      var unflipped = [], flipped = [];
      for(var i = 0; i < shuffledCards.length; i++) {
        if(i < 9) {
          flipped.push(shuffledCards[i]);
        } else {
          unflipped.push(shuffledCards[i]);
        }
      }
      self.model.set('flipped', flipped);
      self.model.set('unflipped', unflipped);
      self.show_preview_flip();
    });
  },
  start_new_game: function() {
    var gridEl = this.$el.find('.robo-memory-grid');
    var cards = [];
    var buffer = 0;
    for(var i = 0; i < 25; i++) {
      var el = $('<div/>');
      gridEl.prepend(el);
      var card = new RoboMemoryCard({
        el: el,
        model: new Backbone.Model({
          random: Math.random(),
          isFlipped: false
        })
      });
      card.on('loaded', function() {
        buffer++;
      });
      card.render();
      cards.push(card);
    }
    this.model.set('cards', cards);
    // wait for images to load successfully
    var self = this;
    function load() {
      setTimeout(function() {
        if(buffer == 25) {
          self.show_cards();
        } else {
          // keep waiting until 
          // robot images have loaded
          load();
        }
      }, 100);
    }
    load();
  },
  render: function() {
    this.$el.html(app.mustache['robo-memory-grid']);
    this.start_new_game();
  }
});

app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render a robot game
    var el = $('<div/>');
    this.$elPage.append(el);

    var robomemory = new RoboMemory({
      el: el,
      model: new Backbone.Model()
    });
    robomemory.render();
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();