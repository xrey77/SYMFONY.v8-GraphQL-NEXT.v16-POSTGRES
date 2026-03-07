import { Document, Page, Text, View, StyleSheet, Image } from '@react-pdf/renderer';

const styles = StyleSheet.create({
  page: { padding: 40, fontSize: 12, paddingBottom: 60 },
  logo: { width: 140, height: 30, marginBottom: 10 },
  headerContainer: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  headerTitle: { fontSize: 18, fontWeight: 'bold' },
  row: { flexDirection: 'row', borderBottom: '1px solid #EEE', padding: 5 },
  cell: { flex: 1 },
  footer: {
    position: 'absolute',
    bottom: 30,
    left: 0,
    right: 0,
    textAlign: 'center',
    fontSize: 10,
    color: 'grey',
  },
});

const baseUrl = process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

export const ReportTemplate = ({ products }: { products: any[] }) => (
  <Document>
    <Page size="A4" style={styles.page}>
      <View style={styles.headerContainer}>
        <View>
        <Image 
          src={`${baseUrl}/images/logo2.png`}
          style={styles.logo} 
        />

          <Text style={styles.headerTitle}>Product Report</Text>
          <Text style={{ fontSize: 10, color: '#666' }}>Generated on {new Date().toLocaleDateString()}</Text>
        </View>
        
      </View>

      <View style={[styles.row, { backgroundColor: '#f0f0f0' }]}>
        <Text style={[styles.cell, { fontWeight: 'bold' }]}>ID</Text>
        <Text style={[styles.cell, { flex: 2, fontWeight: 'bold' }]}>Description</Text>
        <Text style={[styles.cell, { fontWeight: 'bold' }]}>Qty</Text>
        <Text style={[styles.cell, { fontWeight: 'bold' }]}>Price</Text>
      </View>

      {/* Table Body */}
      {products.map((p) => (
        <View key={p.id} style={styles.row} wrap={false}>
          <Text style={styles.cell}>{p.id}</Text>
          <Text style={[styles.cell, { flex: 2 }]}>{p.descriptions}</Text>
          <Text style={styles.cell}>{p.qty} {p.unit}</Text>
          <Text style={styles.cell}>${p.sellprice}</Text>
        </View>
      ))}

      {/* Pagination Footer */}
      <Text 
        style={styles.footer} 
        render={({ pageNumber, totalPages }) => `Page ${pageNumber} of ${totalPages}`} 
        fixed 
      />
    </Page>
  </Document>
);



