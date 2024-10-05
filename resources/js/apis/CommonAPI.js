import axios from 'axios'
import utils from "@/utils/CommonUtils"
import Swal from 'sweetalert2'

export default {
    async jsonApi( uri, method, jsonBody){
        return this.execJsonApi(import.meta.env.VITE_APP_URL + uri, method, jsonBody);
    },

    async uploadPostApi( uri, formData){
        return this.execPostUploadApi(import.meta.env.VITE_APP_URL + uri, formData);
    },
    
    async execJsonApi( uri, method, jsonBody){
        try{
        let payload = {
            method: method, // *GET, POST, PUT, DELETE, etc.
            headers: {
              'Content-Type': 'application/json',
              "Authorization": 'Bearer ' + localStorage.token,
            },
        }

        if(method != 'GET'){
          payload.body = jsonBody;
        }

          let response = await fetch(uri,  payload);
          let jsonResponse = await response.json();
          if(jsonResponse.status == 401){
            Swal.fire('Error!', jsonResponse.message, 'error')
            window.location.href = '/apps/login'
            return
          }
          return jsonResponse;
        }catch(e){       
          // 
          if(uri.includes("rate-quality-input") && e.message == 'Failed to fetch'){
            e.message = 'Network Error'
          }

          utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
        }
    },
    
    async uploadApi( uri, method, formData){
      return this.execUploadApi(import.meta.env.VITE_APP_URL + uri, method, formData);
    },
      
    async execPostUploadApi(uri, formData){
        try{
            let response = await axios.post(uri, formData, {
                'Authorization': 'Bearer ' + localStorage.token,
                'Content-Type': 'multipart/form-data',
            })
            
          let jsonResponse = await response.data;
          return jsonResponse;
        }catch(e){  
          alert(e.message)      
          utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
        }
    },

    async execUploadApi( uri, method, formData){
      try{
        let response = await fetch( uri, {
            method: method, // *GET, POST, PUT, DELETE, etc.
            headers: {
              "Authorization": 'Bearer ' + localStorage.token,
            },
            body: formData
        });
        let jsonResponse = await response.json();
        // 
        return jsonResponse;
      }catch(e){        
        utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
      }
  },
}