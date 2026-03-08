'use client'

import { client } from '@/lib/ApolloClient';
import React, { useEffect, useState, ChangeEvent } from 'react'
import Mfa from './Mfa';
import { gql } from '@apollo/client'

const SIGNIN_USER = gql`
  mutation login($username: String!, $password: String!) {
    loginUser(input: {username: $username, password: $password}) {
      user {
        id
        firstname
        lastname
        email
        mobile
        username
        roles
        isactivated
        isblocked
        userpic
        qrcodeurl
        token
        message
      }    
    }
  }
  `
interface LoginResponse {
  loginUser: {
    user: {
      id: string;
      firstname: string,
      lastname: string,
      email: string,      
      username: string;
      isactivated: number,
      isblocked: number,
      roles: string[];
      userpic: string;
      qrcodeurl: string | null;
      token: string;
      message: string;
    }
  }
}

export default function Login() {
  const [username, setUsername] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [loginmessage, setLoginMessage] = useState<string>('');
  const [dizable, setDizable] = useState<boolean>(false);

  useEffect(() => {
    const initJS = async () => {
      const $ = (await import('jquery')).default;
      (window as any).$ = (window as any).jQuery = $;
    };

    initJS();

  },[])
  
  const submitLogin = async (e: ChangeEvent<HTMLFormElement>) => {
    e.preventDefault();
    setDizable(true);
    setLoginMessage("Please wait..");

    try {

      const { data } = await client.mutate<LoginResponse>({
        mutation: SIGNIN_USER,
        variables: { username: username, password: password }
      });      

      if (data?.loginUser.user) {
        setLoginMessage(data.loginUser.user.message);
        if (data.loginUser.user.qrcodeurl) {
            let xid: any = data.loginUser.user.id;
            const idno = xid.split('/').pop();
            window.sessionStorage.setItem('USERID', idno);
            window.sessionStorage.setItem('ROLES',data.loginUser.user.roles[0]);
            window.sessionStorage.setItem('TOKEN',data.loginUser.user.token);
            let userpic: string = `https://127.0.0.1:8000/users/${data.loginUser.user.userpic}`;
            window.sessionStorage.setItem('USERPIC',userpic);
            $("#loginReset").trigger('click');
            $("#mfamodal").trigger('click')         

        } else {

          let xid: any = data.loginUser.user.id;
          const idno = xid.split('/').pop();
          window.sessionStorage.setItem('USERID', idno);
          window.sessionStorage.setItem('USERNAME',data.loginUser.user.username);
          window.sessionStorage.setItem('TOKEN',data.loginUser.user.token);                        
          window.sessionStorage.setItem('ROLES',data.loginUser.user.roles[0]);
          let userpic: string = `https://127.0.0.1:8000/users/${data.loginUser.user.userpic}`;
          window.sessionStorage.setItem('USERPIC',userpic);
          setTimeout(() => {
            $("#loginReset").trigger('click');
            setLoginMessage('');
              window.location.reload();            
          }, 3000);
        }


      }
    } catch (err: any) {
      setLoginMessage(err.message);
      setTimeout(() => {
        setLoginMessage('');
      }, 3000);
    } finally {
      setDizable(false);
    }
  }

  const closeLogin = () => {
    setUsername("");
    setPassword("");
    window.sessionStorage.clear();
  }

  return (
    <>
      <Mfa />
      <div className="modal fade" id="staticLogin" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticLoginLabel" aria-hidden="true">
        <div className="modal-dialog modal-sm modal-dialog-centered">
          <div className="modal-content">
            <div className="modal-header bg-primary">
              <h1 className="modal-title fs-5 text-white" id="staticLoginLabel">&nbsp;User Signin</h1>
              <button id="close" onClick={closeLogin} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div className="modal-body">
              <form onSubmit={submitLogin}>
                <div className="mb-3">
                  <input disabled={dizable} type="text" required className="form-control" value={username} onChange={e => setUsername(e.target.value)} autoComplete='off' placeholder="enter Username" />
                </div>
                <div className="mb-3">
                  <input disabled={dizable} type="password" required className="form-control" value={password} onChange={e => setPassword(e.target.value)} autoComplete='off' placeholder="enter Password" />
                </div>

                <button disabled={dizable} type="submit" className="btn btn-primary">signin</button>
                <button type="reset" className="btn btn-primary mx-1">reset</button>
                <button id="mfamodal" type="button" className="btn btn-primary hide-mfa" data-bs-toggle="modal" data-bs-target="#staticMFA">MFA</button>

              </form>
            </div>
            <div className="modal-footer">
              <div id="loginMsg" className="w-100 text-left text-danger">{loginmessage}</div>
            </div>
          </div>
        </div>
      </div>
    </>
    );
}