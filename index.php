<!DOCTYPE html>
<html>
  <head>
    <style>
      body, html{
        width: 100%;
        height: 100vh;
        background: linear-gradient(45deg, #000, #123);
        font-family: courier;
        color: #8ff;
        margin: 0;
        overflow: hidden;
      }
      .main{
        width: 100%;
        position: absolute;
        top: 40%;
        left: 50%;
        text-align: center;
        transform: translate(-50%, -50%);
      }
      input[type=text]{
        background: #0004;
        font-size: 20px;
        border: none;
        border-bottom: 1px solid #4f8;
        min-width: 400px;
        color: #fff;
        outline: none;
        text-align: center;
      }
      #status{
       position: absolute;
       width: 100%;
      }
      #downloadButton{
        display: none;
        background: #4f8a;
        font-size: 24px;
        font-family: courier;
        color: #afe;
        text-shadow: 2px 2px 2px #000;
        border: none;
        border-radius: 5px;
        position: absolute;
        left: 50%;
        transform: translatex(-50%);
        margin-top: 60px;
        padding: 5px;
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <script>
      oldTime = 0
      var USER
      timeoutHandles = []
      checkUser=()=>{
        let el = document.querySelector('#status')
        document.querySelector('#downloadButton').style.display='none'
        el.innerHTML = ''
        curTime = (new Date).getTime()
        if((curTime - oldTime) < 1000){
          timeoutHandles.map(v=>clearTimeout(v))
          timeoutHandles = []
        }
        timeoutHandles.push(setTimeout(()=>{
          let user = document.querySelector('#userName').value
          if(user.length){ 
            oldTime = (new Date).getTime()
            fetch('./exists.php?user=' + user).then(text=>text.text()).then(data=>{
              if((curTime - oldTime) < 500){
                if(!!(+data)){
                  el.innerHTML = '<br><span style="color: #0f8;font-size:.9em;">user exists!</span>'
                  USER = user
                  document.querySelector('#downloadButton').style.display='block'
                } else {
                  el.innerHTML = '<br><span style="color: #f66;font-size:.9em;">user not found...</span>'
                  document.querySelector('#downloadButton').style.display = 'none'
                }
              }
            })
          } else {
            document.querySelector('#downloadButton').style.display = 'none'
          }
        }, 1000))
      }
      download=()=>{
        document.querySelector('#downloadButton').style.display = 'none'
        let el = document.querySelector('#status')
        el.innerHTML = '<br><span style="color: #abc;font-size:.9em;">processing...</span>'
        fetch('./scrape.php?user=' + USER).then(text=>text.text()).then(data=>{
          if(!!(data)){
            let a = document.createElement('a')
            a.style.display = 'none'
            document.body.appendChild(a)
            a.href = './csvs/'+USER.replace('%20', '_')+'_collection.csv'
            a.target = "_blank"
            a.click()
            a.parentNode.removeChild(a)
            el.innerHTML = ''
            el.innerHTML = '<span style="color: #abc;font-size:.9em;">done!</span>'
            document.querySelector('#userName').value = ''
          }else{
            let el = document.querySelector('#status')
            el.innerHTML = '<span style="color: #f66;font-size:.9em;">there was a problem - sorry :(</span>'
          }
        })
      }
      submit=e=>{
        if(e.keyCode==13 && document.querySelector('#downloadButton').style.display == 'block'){
          download()
        }
      }
    </script>
    <div class="main">
      <img style="display: inline-block; width:200px;margin-bottom: 40px;" src="https://jsbot.cantelope.org/uploads/1R2qDI.png" /><br>
      download user collection as CSV<br><br>
      <input type="text" id="userName" autofocus onkeypress="submit(event)" placeholder="enter user name (case sensitive)" oninput="checkUser()" spellcheck="false">
      <div id="status"></div>
      <button id="downloadButton" onclick="download()">download CSV</button>
    </div>
  </body>
</html>
