const MSG_LIST = {'MSGCMN0001':'Error backend API \'{1}\', Error : \'{2}\''};

export default {
  // method
  formatRupiah(number){
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR", 
      minimumFractionDigits: (number > 99.9 && number % 1 === 0) ? 0 : 2,
    }).format(number)
  },
  async message(msgId,msgParam){
    let msg = ''+MSG_LIST[msgId];
    let i = 0;
    for(i==0;i<msgParam.length;i++){
      msg = msg.replace('{'+(i+1)+'}',msgParam[i]);
    }
    return msg;
  },
}
