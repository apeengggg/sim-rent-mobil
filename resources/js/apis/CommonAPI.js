import axios from 'axios'
import utils from "@/utils/CommonUtils"
import Swal from 'sweetalert2'

export default {
    async getCommon(url, params, callback) {
        const request = await axios
            .get(process.env.VUE_APP_API_URL + `/api/common${url}${params}`, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.token,
                },
            })
            .then((response) => {
                if (callback) {
                    return callback(response, null)
                }
                return response
            })
            .catch((error) => {
                if (callback) {
                    return callback(error.response, error)
                }
                return error
            })
        return request
    },
    
    async cmnNodeJsonApi( uri, method, jsonBody){
        return this.execJsonApi(process.env.VUE_APP_CMN_NODE_API_URL + uri, method, jsonBody);
    },

    async jsonApi( uri, method, jsonBody){
        return this.execJsonApi(import.meta.env.VITE_APP_URL + uri, method, jsonBody);
    },

    async etrDownloadApi( uri, method, data){
        return await this.execDownloadApi( process.env.VUE_APP_API_URL + uri, method, data);
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
          console.log("🚀 ~ execJsonApi ~ e:", e)
          if(uri.includes("rate-quality-input") && e.message == 'Failed to fetch'){
            e.message = 'Network Error'
          }

          utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
        }
    },

    async execDownloadApi( uri, method, data){
        try{
          let response = await fetch( uri, {
              method: method, // *GET, POST, PUT, DELETE, etc.
              headers: {
                'Content-Type': 'application/json',
                "Authorization": 'Bearer ' + localStorage.token,
              },
              body: data,
          } );
          await response.blob().then(blob => {
            let FILE = window.URL.createObjectURL(blob);
            let docUrl = document.createElement('a');
            docUrl.href = FILE;
            let fileName = response.headers.get('content-disposition')
              .split(';')
              .find(n => n.includes('filename='))
              .replace('filename=', '')
              .trim()
              ;
            docUrl.setAttribute('download', fileName);
            document.body.appendChild(docUrl);
            docUrl.click();
          });
          return ;
        }catch(e){        
          utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
        }
      },

      
    async execPostUploadApi( uri, formData){
        try{
            let response = await axios.post(uri, formData, {
                "Authorization": 'Bearer ' + localStorage.token,
                'Content-Type': 'multipart/form-data',
            })
            console.log("🚀 ~ execUploadApi ~ response:", response)
          let jsonResponse = await response.data;
          return jsonResponse;
        }catch(e){  
          alert(e.message)      
          utils.error(await utils.message('MSGCMN0001',[uri,e.message]));
        }
    },

    async execRefreshToken(method){
        try{
            let response = await fetch(process.env.VUE_APP_SC_API_URL+'/api/authenticate', {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    "Authorization": 'Bearer ' + localStorage.token,
                },
                body: JSON.stringify({
                    userName: process.env.VUE_APP_SC_REFRESH_TOKEN_USERNAME,
                    password: process.env.VUE_APP_SC_REFRESH_TOKEN_PASSWORD
                })
            } );
            let jsonResponse = await response.json();
            console.log('jsonResponse', jsonResponse)
            if(jsonResponse.status==='401'){
                localStorage.token = '';
                localStorage.tokenId = '';
                window.location.href=process.env.VUE_APP_CONTAINER_URL;  
                return;
            }
            localStorage.setItem('token', jsonResponse.token)
            localStorage.setItem('tokenId', jsonResponse.token)
            // await callback()
            return jsonResponse;
            }catch(e){       
                utils.error(await utils.message('MSGCMN0001',[process.env.VUE_APP_SC_API_URL+'/api/authenticate',e.message]));
            }
    },
}