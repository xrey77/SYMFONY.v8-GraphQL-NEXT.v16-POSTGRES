'use client'

import React, { SubmitEvent, useEffect, useState } from 'react'
import Image from 'next/image';
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'
import axios from 'axios';

const api = axios.create({
  baseURL: "https://127.0.0.1:8000/api/graphql"
})

const GET_USERID = gql`
  query getUserId($id: ID!) {
    user(id: $id) {
        id
        firstname
        lastname
        email
        mobile
        username
        isactivated
        isblocked
        userpic
        qrcodeurl
    }
  }
  `
interface GetUserIdResponse {
  user: {
    id: number,
    firstname: string,
    lastname: string,
    email: string,
    mobile: string,
    username: string,
    isactivated: number,
    isblocked: number,
    userpic: string,
    qrcodeurl: string
  }
}

  const UPDATE_PROFILE = gql`
  mutation updateProfile($id: ID!, $firstname: String!, $lastname: String!, $mobile: String!) {
    updateProfileUser(input: {id: $id, firstname: $firstname, lastname: $lastname, mobile: $mobile}) {
       user {
          id
          message
        }
    }
  }
  `
interface UpdateProfileResponse {
  updateProfileUser: {
    user: {
        id: number,
        message: string
    }
  }
}

const UPDATE_PASSWORD = gql`
  mutation updatePassword($id: ID!, $password: String!) {
    updatePasswordUser(input: {id: $id, password: $password}) {
      user {
        id
        message
      }
    }
  }
  `
interface UpdatePasswordResponse {
  updatePasswordUser: {
    user: {
      id: number,
      message: string
    }
  }
}

const ACTIVATE_MFA = gql`
  mutation activateMfa($id: ID!, $twofactorenabled: Boolean!) {
    activateMfaUser(input: {id : $id, twofactorenabled: $twofactorenabled})  {
      user {
        id
        qrcodeurl
        message
      }
    }
  }
  `
interface ActivateMfaResponse{
  activateMfaUser: {
    user: {
      id: number,
      qrcodeurl: string,
      message: string
    }
  }
}

  interface UploadResponse {
  data?: {
    uploadPictureUser: {
      user: {
        id: string;
        userpic: string;
        message: string;
      }
    }
  };
  errors?: Array<{ message: string }>;
}


export default function Profile() {
  const [firstname, setFirstname] = useState<string>('');
  const [lastname, setLastname] = useState<string>('');
  const [email, setEmail] = useState<string>('');
  const [mobile, setMobile] = useState<string>('');
  const [password, setPassword] = useState<string>('');  
  const [confpassword, setConfpassword] = useState<string>('');
  const [message, setMessage] = useState<string>('');
  const [userid, setUserid] = useState<string>('');
  const [profilepic, setProfilepic] = useState<any>(null);
  const [qrcode, setQrcode] = useState<string>('');
  const [token, setToken] = useState<string>('');
  const [showsave, setShowsave] = useState<boolean>(false);

  const fetchData = async (id: string, token: string) => {
    setMessage("Pls wait, retrieving data..")

      try {

        const { data } = await client.query<GetUserIdResponse>({
          query: GET_USERID,
          variables: { 
            id: userid,
          },
        context: {
          headers: {
            authorization: `Bearer ${token}`,
          },
        },          
        });

        if (data?.user) {
          setFirstname(data.user.firstname); 
          setLastname(data.user.lastname);
          setEmail(data.user.email);
          setMobile(data.user.mobile);
          if (data.user.qrcodeurl !== null) {
            setQrcode(data.user.qrcodeurl)
          } else {
            setQrcode('/images/qrcode.png');
          }
          let userpic: string = `https://127.0.0.1:8000/users/${data.user.userpic}`;
          setProfilepic(userpic);
        }
      } catch (err: any) {
        // const msg = err.message.split('\n')[0];
        setMessage(err.message);
        setTimeout(() => {
          setMessage('');
        }, 3000);
      }
  }
  
    useEffect(() => {
      const initJS = async () => {
        const $ = (await import('jquery')).default;
        (window as any).$ = (window as any).jQuery = $;
      };
  
      initJS();
  
    },[])
  
  
  useEffect(() => {
    jQuery("#cpwd").hide();
    jQuery("#qcode").hide();
    jQuery("#qcode-info").hide();
    const idno = window.sessionStorage.getItem('USERID');
    if (idno !== null) {
      setUserid(idno);
    }

    const tkn = window.sessionStorage.getItem('TOKEN');
    if (tkn !== null) {
      setToken(tkn);
    } 
    if (userid.length > 0) {
      fetchData(userid, token);      
    }
    setMessage('');
  },[userid, token]);

  const updateProfile =  async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault(); 

      try {

        const { data } = await client.mutate<UpdateProfileResponse>({
          mutation: UPDATE_PROFILE,
          variables: { 
            id: userid,
            firstname: firstname,
            lastname: lastname,
            mobile: mobile
          },
        context: {
          headers: {
            authorization: `Bearer ${token}`,
          },
        },          
        });
        if (data?.updateProfileUser) {
          setMessage(data.updateProfileUser.user.message);
          setTimeout(() => { setMessage(''); }, 3000);
        }
      } catch (err: any) {
        // const msg = err.message.split('\n')[0];
        console.log(err.message);
        alert("error");
        setMessage(err.message);
        setTimeout(() => { setMessage(''); }, 3000);
      }
  }

  const changePwd = () => {
    if (jQuery('#chgPwd').is(":checked")) {    
      jQuery('#mfacode').prop('checked', false);
      jQuery("#qcode-info").hide();
      jQuery("#qcode").hide();
      jQuery("#cpwd").show();
      setShowsave(true);
    } 
    else {
      jQuery("#cpwd").hide();
      setShowsave(false);
      setPassword('');
      setConfpassword('');
    }
  }

  const enableMFA = () => {
    if (jQuery('#mfacode').is(":checked")) {    
      setShowsave(true);
      // setQrcode('');
      if (jQuery('#chgPwd').is(":checked")) {    
        setPassword("");
        setConfpassword("");
        jQuery('#chgPwd').prop('checked', false);
        jQuery("#cpwd").hide();
      }
      jQuery("#qcode").show();
      jQuery("#qcode-info").show();
    } else {
      setShowsave(false);
      jQuery("#qcode").hide();
      jQuery("#qcode-info").hide();      
    }
  }

  //
  const changeProfilepic = async (event: SubmitEvent<HTMLInputElement>) => {
    event.preventDefault();
    const file = event.target.files?.[0] || null;

    if (file) {
      const pix = URL.createObjectURL(file);
      jQuery('#userpic').attr('src', pix);

      const operations = JSON.stringify({
          query: `
              mutation uploadPicture($id: ID!, $file: Upload!) {
                  uploadPictureUser(input: {id: $id, file: $file}) {
                      user {
                        id
                        userpic
                        message
                      }
                  }
              }
          `,
          variables: { id: `/api/users/${userid}`, file: null }
      });

      const map = JSON.stringify({ "0": ["variables.file"] });
      const formData = new FormData();
      formData.append("operations", operations);
      formData.append("map", map);
      formData.append("0", file); 

      try {

            const res = await api.post<UploadResponse>('', formData, {
                headers: {
                    'apollo-require-preflight': 'true',
                    Authorization: `Bearer ${token}`
                  }
            });

            if (res.data.errors) {
              setMessage(res.data.errors[0].message);
            } else if (res.data.data) {
              const user = res.data.data.uploadPictureUser.user;
              setMessage(user.message);
              setProfilepic(`https://127.0.0.1:8000/users/${user.userpic}`);
            }              
            setTimeout(() => setMessage(''), 3000);

        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            console.log(errorMsg);
            setMessage(errorMsg);
            setTimeout(() => setMessage(''), 3000);
        }
    }
  }

  const activateMFA =  async () => {

      try {

        const { data } = await client.mutate<ActivateMfaResponse>({
          mutation: ACTIVATE_MFA,
          variables: { 
            id: userid,
            twofactorenabled: true
          },
        context: {
          headers: {
            authorization: `Bearer ${token}`,
          },
        },          
        });
        if (data?.activateMfaUser) {
          setMessage(data.activateMfaUser.user.message);
          setQrcode(data.activateMfaUser.user.qrcodeurl);
          setTimeout(() => { setMessage(''); }, 3000);
        }
      } catch (err: any) {
        setMessage(err.message);
        setTimeout(() => { setMessage(''); }, 3000);
      }

  }

  const deactivateMFA = async () => {
      try {

        const { data } = await client.mutate<ActivateMfaResponse>({
          mutation: ACTIVATE_MFA,
          variables: { 
            id: userid,
            twofactorenabled: false
          },
        context: {
          headers: {
            authorization: `Bearer ${token}`,
          },
        },          
        });
        if (data?.activateMfaUser) {
          setMessage(data.activateMfaUser.user.message);
          setQrcode("/images/qrcode.png");
          setTimeout(() => { setMessage(''); }, 3000);
        }
      } catch (err: any) {
        setMessage(err.message);
        setTimeout(() => { setMessage(''); }, 3000);
      }

  }

  const changePassword = async () => {
    if (password === '') {
      setMessage("Please enter new Pasword.");
        setTimeout(() => {
            setMessage('');
        },3000);
        return;
    }
    if (confpassword === '') {
      setMessage("Please enter new Pasword confirmation.");
        setTimeout(() => {
            setMessage('');
        },3000);
        return;            
    }

    if (password !== confpassword) {
      setMessage("new Password does not matched.");
        setTimeout(() => {
            setMessage('');
        },3000);
        return;            
    }

      try {

        const { data } = await client.mutate<UpdatePasswordResponse>({
          mutation: UPDATE_PASSWORD,
          variables: { 
            id: userid,
            password: password
          },
        context: {
          headers: {
            authorization: `Bearer ${token}`,
          },
        },          
        });

        if (data?.updatePasswordUser) {
          setMessage(data.updatePasswordUser.user.message);
          setTimeout(() => { setMessage(''); }, 3000);
        }
      } catch (err: any) {
        setMessage(err.message);
        setTimeout(() => { setMessage(''); }, 3000);
      }
}

  return (
    <div className='container-fluid bg-black'>
      <div className="card mb-10">
        <div className='card-header bg-primary text-white'>
             <strong>USER PROFILE ID NO.</strong>&nbsp; {userid}
        </div>
        <div className="card-body bg-warning">

          <form onSubmit={updateProfile} encType='multipart/form-data' autoComplete='off' method='POST'>
            <div className='row'>
              <div className='col'>
                <div className="mb-3">
                  <label htmlFor="firstname" className="form-label">First Name</label>
                  <input type="text" className="form-control input-border" value={firstname} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setFirstname(e.target.value)}/>
                </div>            
                <div className="mb-3">
                  <label htmlFor="lastname" className="form-label">Last Name</label>
                  <input type="text" className="form-control input-border" value={lastname} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setLastname(e.target.value)}/>
                </div>            
              </div>
              <div className='col'>
                    <div className="mb-3 mt-2">
                      <div className='user-pix-box'>
                        <img id="userpic" src={profilepic}  className='userprofile' alt={''}/>
                      </div>
                      <input type="file" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setProfilepic(e.target.value)} className="form-control form-control-sm" id="profilepic"/>
                    </div>                                    
              </div>
            </div>

            <div className='row'>
              <div className='col'>
                <div className="mb-3">
                  <label htmlFor="email" className="form-label">Email Address</label>
                  <input type="email" readOnly className="form-control input-border" value={email}/>
                </div>            
              </div>
              <div className='col'>
                <div className="mb-3">
                  <label htmlFor="mobile" className="form-label">Mobile No.</label>
                  <input type="text" className="form-control input-border" value={mobile} onChange={(e: React.ChangeEvent<HTMLInputElement>) => setMobile(e.target.value)}/>
                </div>            
              </div>              
            </div>

            <div className='row'>
              <div className='col'>

                  <div className="form-check">
                    <input className="form-check-input cb-border" type="checkbox" value="" onChange={changePwd} id="chgPwd"/>
                    <label className="form-check-label" htmlFor="flexCheckDefault">
                        Change Password
                    </label>
                  </div>
                  <div id="cpwd">
                     <div className="mb-3">
                       <input type="password" className="form-control" value={password} 
                       onChange={(e: React.ChangeEvent<HTMLInputElement>) => setPassword(e.target.value)} placeholder='enter new password.'/>
                     </div>            
                     <div className="mb-3">
                       <input type="password" className="form-control" value={confpassword} 
                       onChange={(e: React.ChangeEvent<HTMLInputElement>) => setConfpassword(e.target.value)} placeholder='enter new password confirmation.'/>
                     </div>            
                     <button onClick={changePassword} type="button" className='btn btn-primary'>change password</button>
                  </div> 
                  <div id="qcode">
                    {
                      qrcode !== '' ?
                        <Image width={200} height={200} className='qrcode2' src={qrcode} alt='' />
                    :
                        <Image width={200} height={200} className='qrcode1' src="/images/qrcode.png" alt='' />
                    }
                  </div>
              </div>
              <div className='col'>
                <div className="form-check">
                  <input className="form-check-input cb-border" type="checkbox" value="" onChange={enableMFA} id="mfacode"/>
                  <label className="form-check-label" htmlFor="flexCheckDefault">
                      2-Factor Authentication
                  </label>
                </div>
                <div id="qcode-info">
                    <p className='text-danger'><strong>Requirements</strong></p>
                    <p>You need to install <strong>Google or Microsoft Authenticator</strong> in your Mobile Phone, once installed, click Enable Button below, and <strong>SCAN QR CODE</strong>, next time you login, another dialog window will appear, then enter the <strong>OTP CODE</strong> from your Mobile Phone in order for you to login.</p>
                    <div className='row'>
                      <div className='col-2'>
                        <button onClick={activateMFA} type="button" className='btn btn-primary'>Enable</button>
                      </div>
                      <div className='col-2'>
                        <button onClick={deactivateMFA} type="button" className='btn btn-secondary'>Disable</button>                        
                      </div>
                    </div>
                </div>
              </div>              
            </div>
            { showsave === false ? (
              <button type='submit' className='btn btn-primary mt-3'>save</button>
            ):
                null
            }
          </form>    
        </div>
        <div className='card-footer bg-warning'> 
          <div className='w-100 text-danger'>{message}</div>
        </div>
      </div>
      <br/><br/>

    </div>
  )
}