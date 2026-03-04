'use client'
import React from 'react';
import BootstrapClient from '../components/BootstrapClient';
import Header from './header'

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body>
        <Header />
        <main>{children}</main>
        <BootstrapClient />
      </body>
    </html>
  );
}