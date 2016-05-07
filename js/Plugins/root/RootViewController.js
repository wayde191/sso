/**
  *@Version : 1.0
  *@Author : Wayde Sun
  *@Time : 2016.5.4
  */

  ih.defineClass('ih.plugins.rootViewController', null, null, function(ROOT, root){
  
    root.prototype.init = function(){
      this.dm = new ih.plugins.rootDataModel();
      this.dm.delegate = this;
      this.setupErrorinfo();
      this.setupClickEvent();
    };
    
    root.prototype.setupClickEvent = function(){
      var me = this;
      $('#accountpassword').bind('keypress',function(event){
        if(event.keyCode == "13") {
          me.onSignInBtnClicked();
        }
      });

      $("#ih-login-btn").click(ih.$F(function(){
        this.onSignInBtnClicked();
      }).bind(this));
      $("#ih-forgetPwd-btn").click(ih.$F(function(){
        this.onForgetPwdMaskBtnClicked();
      }).bind(this));

      $("#mask-button").click(ih.$F(function(){
        this.onCloseMaskBtnClicked();
      }).bind(this));

      // Register
      $("#register-cancel").click(ih.$F(function(){
        this.onCloseMaskBtnClicked();
      }).bind(this));
      $("#register-sure").click(ih.$F(function(){
        this.onRegisterSureBtnClicked();
      }).bind(this));
    };
    
    root.prototype.onRegisterSureBtnClicked = function(){
      var accountName = $("#accountname")[0].value;
      var accountPassword = $("#accountpassword")[0].value;
      var confirmPassword = $("#confirmpassword")[0].value;
      
      if(!accountName || !accountPassword || !confirmPassword){
        this.showErrorMessage({title:"温馨提示", text:"三项均不能为空"});
        return;
      } else if (!this.dm.checkMobile(accountName)){
        this.showErrorMessage({title:"温馨提示", text:"请输入正确的手机号"});
        return;
      } else if(accountPassword != confirmPassword) {
        this.showErrorMessage({title:"温馨提示", text:"密码确认不相等，请重新输入"});
        $("#accountpassword").val("");
        $("#confirmpassword").val("");
        return;
      }
      
      ih.plugins.rootPlugin.showMaskSpinner();
      this.dm.doRegister({ihakulaID:accountName, password:accountPassword, confirmPwd:confirmPassword});
    };
    
    root.prototype.registerSuccess = function(){
      ih.plugins.rootPlugin.hideMaskSpinner();
      this.showMessage({title:"温馨提示", text:"注册成功，请登录"});
      var tempF = function(){
        window.location.href = '/sso/login.html';
      };
      window.setTimeout(tempF, 2000);
    };
    
    root.prototype.registerFailed = function(errorCode){
      ih.plugins.rootPlugin.hideMaskSpinner();
      this.showErrorMessage({title:"温馨提示", text:this.errorInfo[errorCode]});
    };
    
    root.prototype.onSignInBtnClicked = function(){
      var accountName = $("#accountname")[0].value;
      var accountPassword = $("#accountpassword")[0].value;
      
      if(!accountName){
        this.showErrorMessage({title:"温馨提示", text:"请输入用户名"});
        return;
      } else if(!accountPassword){
        this.showErrorMessage({title:"", text:"请输入密码"});
        return;
      }

      ih.plugins.rootPlugin.showMaskSpinner();
      this.dm.doLogin({ihakulaID:accountName, password:accountPassword});
    };
    
    root.prototype.loginSuccess = function(){
      ih.plugins.rootPlugin.hideMaskSpinner();
      this.setUserinfo();
      this.onCloseMaskBtnClicked();
      $('.forwarding').css("display", "block");
    };
    
    root.prototype.loginFailed = function(errorCode){
      ih.plugins.rootPlugin.hideMaskSpinner();
      this.showErrorMessage({title:"温馨提示", text:this.errorInfo[errorCode]});
    };
    
    root.prototype.setUserinfo = function(){

    };
    
    root.prototype.setUserLogout = function(){
      this.dm.sysUser.logout();
      $("#ih-hi").html("");
      $("#ih-login-button").html("Login");
      $("#ih-login-button").unbind("click");
      $("#ih-login-button").click(ih.$F(function(){
        this.onLoginBtnClicked();
      }).bind(this));
    };
    
    root.prototype.onForgetPwdMaskBtnClicked = function(){
      this.showMessage({title:"温馨提示", text:"Coming soon"});
    };
    
    root.prototype.onCloseMaskBtnClicked = function(){
      $("#ds_container").addAnimation("bounceOutUp");
      $("#ih-mask").addAnimation("fadeOut");
      var tempF = function(){
        $("#ih-mask").css("display", "none");
      };
      window.setTimeout(tempF, 300);
    };

    root.prototype.showErrorMessage = function(dialogMsg){
      this.showErrorBlock();
      this.hideInfoBlock();
      $('.error .dserror').html(dialogMsg.text);
    };

    root.prototype.showMessage = function(dialogMsg){
      this.hideErrorBlock();
      this.showInfoBlock();
      $('.information .dsinfo').html(dialogMsg.text);
    };

    root.prototype.showErrorBlock = function(){
      $('.error').css("display", "block");
    };

    root.prototype.hideErrorBlock = function(){
      $('.error').css("display", "none");
    };

    root.prototype.showInfoBlock = function(){
      $('.information').css("display", "block");
    };

    root.prototype.hideInfoBlock = function(){
      $('.information').css("display", "none");
    };
    
    root.prototype.showDialogMessage = function(dialogMsg){
      // Dialog
        $('#dialog').dialog({
            autoOpen: false,
            width: 600,
            title: dialogMsg.title,
            buttons: {
                "Sure": function() {
                    $(this).dialog("close");
                }
            }
        });

        // Dialog Link
        $('#dialog').html(dialogMsg.text).dialog('open');
    };
    
    root.prototype.setupErrorinfo = function(){
      this.errorInfo = {
        1101 : "请确保是合法访问",
        1201 : "该用户已经登录",
        905 : "该用户不存在",
        904 : "密码错误",
        903 : "该手机号已经被注册"
      };
    };

  });

  window.ihSysEngine.pubsub.publish("ihRootPluginReady");
