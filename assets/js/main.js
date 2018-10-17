
    $( document ).ready(function() {

      var username, uuid, key;
      location.hash = "#";

      $(window).bind( 'hashchange', function(e) {

        var hash = location.hash.replace("#","");

        switch(hash) {
          case "check":
              if(username !== undefined && uuid !== undefined && key !== undefined) {
                $('.register').children('form').each(function () {
                  if(this.id !== hash + "-form") {
                    $(this).fadeOut();
                    console.log("Fadeout: " + this.id);
                  } else {
                    $(this).delay(500).fadeIn();
                    console.log("FadeIN: " + this.id);
                  }
                });
              } else {
                location.hash = "#";
              }
              break;
          case "success":
              $('.register').children('form').each(function () {
                if(this.id !== hash + "-form") {
                  $(this).fadeOut();
                } else {
                  $(this).delay(500).fadeIn();
                }
              });

              new Vivus('svg-success', {duration: 120, start: "inViewport"});
              break;
          default:
            $('.register').children('form').each(function () {
              if(this.id !== "register-form") {
                $(this).fadeOut();
              } else {
                $(this).delay(500).fadeIn();
              }
            });
        }
      });



      $( "#register-form" ).submit(function( e ) {
        e.preventDefault();

        username = $( "#register-username" ).val();
        key = $( "#register-key" ).val();

        $( ".errorMessage" ).text("");

        result = getUUID(username);

        if(result !== undefined) {
          uuid = result.id
          username = result.name;

          if($( "#register-username" ).attr( "error" )) {
            $( "#register-username" ).removeAttr("error");
          }
          if($( "#register-key" ).attr( "error" )) {
            $( "#register-key" ).removeAttr("error");
          }

          $( "#check-avatar" ).html('<img src="https://crafatar.com/avatars/' + uuid + '" class="img-fluid" />');
          $( "#check-name" ).html('<b>' + username + '</b>');
          $( "#check-id" ).html('<b>' + uuid + '</b>');
          $( "#check-key" ).val(key);

          result = checkData(uuid, key);

          if(result.status) {
            location.hash = "#check";
          } else {
            if(result.errorField == "key") {
              $( "#register-key" ).attr("error", "true").animateCss("shake");
            } else if(result.errorField == "username") {
              $( "#register-username" ).attr("error", "true").animateCss("shake");
            }
            $( ".errorMessage" ).text(result.msg);
          }

        } else {
          username = undefined;
          $( "#register-username" ).attr("error", "true").animateCss("shake");
          location.hash = "#";
        }
        ////////////////////////////////////grecaptcha.reset();
      });

      $( "#check-form" ).submit(function( e ) {
        e.preventDefault();

        if(redeem(uuid, key) !== undefined) {
          location.hash = "#success";
        }
      });


      $( ".copy-ip" ).click(function( e ) {
        copy(this);
      });

    });

    function getUUID(username) {
      var result;
      $.ajax({
      dataType: "json",
      async: false,
      url: "https://api.minetools.eu/uuid/" + username,
      success: function(data) {
          if(data.status == "OK") {
            result = data;
          }
        }
      });
      return result;
    }

    function checkData(uuid, key) {
      var result;
      $.ajax({
      dataType: "json",
      async: false,
      type: "POST",
      data: {
        uuid: uuid,
        key: key,
        g_recaptcha_response: "asdasd"//grecaptcha.getResponse()
      },
      url: "http://" + window.location.hostname + "/api/?check=1",
      success: function(data) {
          result = data;
        }
      });
      return result;
    }

    function redeem() {
      var result;
      $.ajax({
      dataType: "json",
      async: false,
      type: "GET",
      url: "http://" + window.location.hostname + "/api/?redeem=1",
      success: function(data) {
          result = data;
        }
      });
      console.log(result);
      return result;
    }

    function copy(that){
      var inp = document.createElement("input");
      document.body.appendChild(inp)
      inp.value = that.textContent
      inp.select();
      document.execCommand("copy",false);
      inp.remove();
    }

    $.fn.extend({
    animateCss: function (animationName) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        this.addClass('animated ' + animationName).one(animationEnd, function() {
            $(this).removeClass('animated ' + animationName);
        });
        return this;
    }
});
